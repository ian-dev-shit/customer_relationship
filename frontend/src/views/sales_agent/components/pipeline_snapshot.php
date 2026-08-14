<!-- PIPELINE SNAPSHOT -->
<div class="lg:col-span-8 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
  <div>
    <div class="flex items-center justify-between mb-1">
      <h2 class="text-xl font-bold text-slate-900 tracking-tight">Pipeline Snapshot</h2>
      <a href="/kanban" class="text-xs font-bold text-purple-600 hover:text-purple-700">Open board →</a>
    </div>
    <p class="text-xs text-slate-400 mb-8">Leads by stage, this month</p>

    <div class="space-y-6">
      
      <!-- New Inquiry -->
      <div>
        <div class="flex justify-between items-center text-xs font-bold text-slate-800 mb-2">
          <span class="text-sm">New Inquiry</span>
          <span class="text-slate-500 font-normal"><?= htmlspecialchars((string)$pipeline['new_inquiry']['count']) ?></span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3">
          <div class="bg-purple-400 h-3 rounded-full" style="width: <?= $pipeline['new_inquiry']['percentage'] ?>%;"></div>
        </div>
      </div>

      <!-- Qualifying -->
      <div>
        <div class="flex justify-between items-center text-xs font-bold text-slate-800 mb-2">
          <span class="text-sm">Qualifying</span>
          <span class="text-slate-500 font-normal"><?= htmlspecialchars((string)$pipeline['qualifying']['count']) ?></span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3">
          <div class="bg-amber-400 h-3 rounded-full" style="width: <?= $pipeline['qualifying']['percentage'] ?>%;"></div>
        </div>
      </div>

      <!-- Quote Sent -->
      <div>
        <div class="flex justify-between items-center text-xs font-bold text-slate-800 mb-2">
          <span class="text-sm">Quote Sent</span>
          <span class="text-slate-500 font-normal"><?= htmlspecialchars((string)$pipeline['quote_sent']['count']) ?></span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3">
          <div class="bg-purple-400 h-3 rounded-full" style="width: <?= $pipeline['quote_sent']['percentage'] ?>%;"></div>
        </div>
      </div>

      <!-- Negotiation -->
      <div>
        <div class="flex justify-between items-center text-xs font-bold text-slate-800 mb-2">
          <span class="text-sm">Negotiation</span>
          <span class="text-slate-500 font-normal"><?= htmlspecialchars((string)$pipeline['negotiation']['count']) ?></span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3">
          <div class="bg-amber-400 h-3 rounded-full" style="width: <?= $pipeline['negotiation']['percentage'] ?>%;"></div>
        </div>
      </div>

      <!-- Won (MTD) -->
      <div>
        <div class="flex justify-between items-center text-xs font-bold text-slate-800 mb-2">
          <span class="text-sm">Won (MTD)</span>
          <span class="text-slate-500 font-normal"><?= htmlspecialchars((string)$pipeline['won_mtd']['count']) ?></span>
        </div>
        <div class="w-full bg-slate-200 rounded-full h-3">
          <div class="bg-emerald-500 h-3 rounded-full" style="width: <?= $pipeline['won_mtd']['percentage'] ?>%;"></div>
        </div>
      </div>

    </div>
  </div>
</div>