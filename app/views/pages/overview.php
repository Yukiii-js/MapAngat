<div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
    <table class="w-full text-left border-collapse text-sm">
        <thead>
            <tr class="bg-slate-50/75 text-slate-400 text-xs font-bold uppercase border-b border-slate-100">
                <th class="p-4">Application ID</th>
                <th class="p-4">Applicant / Site</th>
                <th class="p-4">Barangay</th>
                <th class="p-4">Submitted</th>
                <th class="p-4">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php if (!empty($recent_clearances) && is_array($recent_clearances)): ?>
                <?php foreach ($recent_clearances as $row): ?>
                    <?php 
                        // Normalize LavaLust database result (whether array or stdClass object)
                        $row = (array) $row; 
                    ?>
                    <tr class="hover:bg-slate-50/50">
                        <!-- LavaLust html_escape() global helper for XSS protection -->
                        <td class="p-4 font-bold text-primary">
                            <?= html_escape($row['application_id'] ?? $row['permit_number'] ?? $row['id'] ?? 'N/A') ?>
                        </td>

                        <td class="p-4 text-slate-700 font-medium">
                            <?= html_escape($row['project_type'] ?? $row['project_title'] ?? $row['applicant_name'] ?? 'N/A') ?>
                        </td>

                        <td class="p-4 text-slate-600">
                            <?= html_escape($row['barangay'] ?? $row['barangay_name'] ?? 'N/A') ?>
                        </td>

                        <td class="p-4 text-slate-600">
                            <?= !empty($row['created_at']) ? date('M d, Y', strtotime($row['created_at'])) : 'N/A' ?>
                        </td>

                        <td class="p-4">
                            <?php 
                                $status = $row['status'] ?? 'Pending';
                                $badge_class = match(strtolower($status)) {
                                    'approved'     => 'bg-emerald-100 text-emerald-800',
                                    'rejected'     => 'bg-rose-100 text-rose-800',
                                    'under review' => 'bg-blue-100 text-blue-800',
                                    default        => 'bg-amber-100 text-amber-800',
                                };
                            ?>
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full <?= $badge_class ?>">
                                <?= html_escape($status) ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="p-4 text-center text-slate-500">
                        No recent clearances found.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>