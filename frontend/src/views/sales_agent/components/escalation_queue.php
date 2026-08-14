<!-- AI ESCALATION QUEUE -->
<div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
  <div class="flex items-center justify-between mb-1">
    <h2 class="text-sm font-bold text-slate-900">AI Escalation Queue</h2>
    <a href="/ai-escalations" class="text-xs font-bold text-purple-600 hover:text-purple-700">View All →</a>
  </div>
  <p class="text-[11px] text-slate-400 mb-4">Below-threshold inquiries needing you</p>

  <div class="space-y-3">
    <?php if (!empty($escalations)): ?>
      <?php foreach ($escalations as $item): ?>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex gap-3 items-start">
          <div class="p-2 rounded-lg bg-rose-100 text-rose-500 shrink-0 mt-0.5">
            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
              <h3 class="text-xs font-bold text-slate-800 truncate">
                <?= htmlspecialchars($item['company_name'] ?? $item['contact_person'] ?? 'Unknown Lead') ?>
              </h3>
            </div>
            <p class="text-[11px] text-slate-500 leading-snug mt-1 line-clamp-2">
              <?= htmlspecialchars($item['cargo_details'] ?? $item['subject'] ?? 'New lead inquiry.') ?>
            </p>
            <span class="text-[10px] text-slate-400 block mt-1.5">
              <?= htmlspecialchars($item['created_at'] ?? '') ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-xs text-slate-400 italic py-4 text-center">No pending escalations.</p>
    <?php endif; ?>
  </div>
</div>