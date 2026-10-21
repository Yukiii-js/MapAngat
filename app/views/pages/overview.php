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
            <?php foreach($recent_clearances as $row): ?>
            <tr class="hover:bg-slate-50/50">
                <td class="p-4 font-bold text-primary"><?= html_escape($row['application_id']) ?></td>
                <td class="p-4 text-slate-700 font-medium"><?= html_escape($row['project_type']) ?></td>
                <td class="p-4 text-slate-600"><?= html_escape($row['barangay']) ?></td>
                <td class="p-4 text-slate-600"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                <td class="p-4">
                    <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800">
                        <?= html_escape($row['status']) ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>