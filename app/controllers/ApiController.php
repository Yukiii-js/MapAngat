<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class ApiController extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper('url');
        $this->call->library('api');
        $this->call->model('Clearance_model');
        $this->call->model('Project_model');
    }

    private function json_response($data, $status = 200) {
        $this->api->respond($data, $status);
    }

    public function health() {
        $this->api->require_method('GET');
        $this->json_response([
            'status' => 'ok',
            'app' => 'MapAngat API',
            'timestamp' => date('c')
        ]);
    }

    private function get_user_by_username($username) {
        $db = lava_instance()->db;
        $stmt = $db->raw(
            "SELECT u.*, COALESCE(r.role_name, 'Admin') AS role
             FROM users u
             LEFT JOIN roles r ON r.id = u.role_id
             WHERE u.username = ? LIMIT 1",
            [$username]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($user) && !empty($user) ? $user : null;
    }

    private function get_user_by_id($id) {
        $db = lava_instance()->db;
        $stmt = $db->raw(
            "SELECT u.*, COALESCE(r.role_name, 'Admin') AS role
             FROM users u
             LEFT JOIN roles r ON r.id = u.role_id
             WHERE u.id = ? LIMIT 1",
            [$id]
        );

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($user) && !empty($user) ? $user : null;
    }

    private function table_exists($table) {
        $stmt = lava_instance()->db->raw(
            "SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ?",
            [$table]
        );

        return (int) $stmt->fetchColumn() > 0;
    }

    public function login() {
        $this->api->require_method('POST');
        $body = $this->api->body();
        $username = trim((string) ($body['username'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($username === '' || $password === '') {
            $this->api->respond_error('Username and password are required', 400);
        }

        $user = $this->get_user_by_username($username);

        if (!$user || !isset($user['password']) || !password_verify($password, $user['password'])) {
            $this->api->respond_error('Invalid username or password', 401);
        }

        $role = $user['role'] ?? 'admin';
        $token_payload = [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'role' => $role,
            'scopes' => ['read', 'write']
        ];

        $tokens = $this->api->issue_tokens($token_payload);

        $this->json_response([
            'message' => 'Login successful',
            'user' => [
                'id' => (int) $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'] ?? '',
                'role' => $role,
                'email' => $user['email'] ?? ''
            ],
            'tokens' => $tokens
        ]);
    }

    public function me() {
        $this->api->require_method('GET');
        $payload = $this->api->require_jwt();

        $user = $this->get_user_by_id((int) ($payload['sub'] ?? 0));

        $this->json_response([
            'user' => [
                'id' => (int) ($user['id'] ?? 0),
                'username' => $user['username'] ?? '',
                'full_name' => $user['full_name'] ?? '',
                'role' => $user['role'] ?? 'admin'
            ]
        ]);
    }

    public function refresh() {
        $this->api->require_method('POST');
        $body = $this->api->body();
        $refresh_token = trim((string) ($body['refresh_token'] ?? ''));

        if ($refresh_token === '') {
            $this->api->respond_error('Refresh token is required', 400);
        }

        $this->api->refresh_access_token($refresh_token);
    }

    public function dashboard_stats() {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $clearance_counts = $this->Clearance_model->get_counts_by_status();
        $project_counts = $this->Project_model->get_counts_by_status();

        $this->json_response([
            'counts' => [
                'clearance_pending' => $clearance_counts['pending'] ?? 0,
                'clearance_review' => $clearance_counts['review'] ?? 0,
                'clearance_approved' => $clearance_counts['approved'] ?? 0,
                'projects_total' => $project_counts['total'] ?? 0,
                'projects_on_track' => $project_counts['on_track'] ?? 0,
                'projects_at_risk' => $project_counts['at_risk'] ?? 0,
                'projects_delayed' => $project_counts['delayed'] ?? 0,
            ]
        ]);
    }

    public function projects() {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $projects = $this->Project_model->get_all();
        $this->json_response(['projects' => $projects]);
    }

    public function clearances() {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $clearances = $this->Clearance_model->get_all();
        $this->json_response(['clearances' => $clearances]);
    }

    public function map_points() {
        $this->api->require_method('GET');
        $this->api->require_jwt();

        $points = [];
        $projectRows = $this->Project_model->get_all();
        foreach ($projectRows as $row) {
            if (empty($row['lat']) || empty($row['lng'])) {
                continue;
            }

            $points[] = [
                'id' => 'project-' . ($row['id'] ?? uniqid()),
                'name' => $row['project_name'] ?? 'Project',
                'type' => 'project',
                'lat' => (float) ($row['lat'] ?? 0),
                'lng' => (float) ($row['lng'] ?? 0),
                'barangay' => $row['barangay'] ?? '',
                'status' => $row['status'] ?? 'Unknown'
            ];
        }

        $clearanceRows = $this->Clearance_model->get_all();
        foreach ($clearanceRows as $row) {
            if (empty($row['lat']) || empty($row['lng'])) {
                continue;
            }

            $points[] = [
                'id' => 'clearance-' . ($row['id'] ?? uniqid()),
                'name' => ($row['applicant_name'] ?? 'Applicant') . ' - ' . ($row['project_type'] ?? $row['business_name'] ?? 'Permit'),
                'type' => 'clearance',
                'lat' => (float) ($row['lat'] ?? 0),
                'lng' => (float) ($row['lng'] ?? 0),
                'barangay' => $row['barangay'] ?? '',
                'status' => $row['status'] ?? 'Pending'
            ];
        }

        if ($this->table_exists('field_compliance_permits')) {
            $stmt = lava_instance()->db->raw(
                "SELECT p.id, p.permit_number, p.project_title, p.status,
                        p.location_latitude AS lat, p.location_longitude AS lng,
                        b.barangay_name AS barangay
                 FROM field_compliance_permits p
                 LEFT JOIN barangays b ON b.id = p.barangay_id
                 WHERE p.location_latitude IS NOT NULL AND p.location_longitude IS NOT NULL"
            );

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $points[] = [
                    'id' => 'permit-' . $row['id'],
                    'reference' => $row['permit_number'],
                    'name' => $row['project_title'],
                    'type' => 'permit',
                    'lat' => (float) $row['lat'],
                    'lng' => (float) $row['lng'],
                    'barangay' => $row['barangay'] ?? '',
                    'status' => $row['status'] ?? 'Unknown',
                ];
            }
        }

        if ($this->table_exists('hazard_reports')) {
            $stmt = lava_instance()->db->raw(
                "SELECT h.id, h.report_code, h.title, h.status, h.severity_level,
                        h.latitude AS lat, h.longitude AS lng,
                        b.barangay_name AS barangay
                 FROM hazard_reports h
                 LEFT JOIN barangays b ON b.id = h.barangay_id
                 WHERE h.latitude IS NOT NULL AND h.longitude IS NOT NULL"
            );

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $points[] = [
                    'id' => 'report-' . $row['id'],
                    'reference' => $row['report_code'],
                    'name' => $row['title'],
                    'type' => 'report',
                    'lat' => (float) $row['lat'],
                    'lng' => (float) $row['lng'],
                    'barangay' => $row['barangay'] ?? '',
                    'status' => $row['status'] ?? 'Pending',
                    'severity' => $row['severity_level'] ?? 'Unknown',
                ];
            }
        }

        $this->json_response(['points' => $points]);
    }
}
