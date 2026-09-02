// Drag and Drop Logic
function dragLead(ev, leadId) {
  ev.dataTransfer.setData("text/plain", leadId);
}

function allowDrop(ev) {
  ev.preventDefault();
}

async function dropLead(ev, targetStage) {
  ev.preventDefault();
  const leadId = ev.dataTransfer.getData("text/plain");
  if (leadId) {
    await updateLeadStatus(leadId, targetStage);
  }
}

// Gamitin ang flexible URL 
const FASTAPI_BASE_URL = window.APP_CONFIG.API_BASE_URL;

async function updateLeadStatus(leadId, newStatus) {
  try {
    // Kunin ang agent_id mula sa body attribute 
    const agentId = document.body.getAttribute('data-agent-id') || null;

    const response = await fetch(`${FASTAPI_BASE_URL}/api/v1/leads/${leadId}/status`, {
      method: 'PATCH',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        status: newStatus,
        assigned_agent_id: agentId 
      })
    });

    if (response.ok) {
      showToast(`Lead moved to ${newStatus.replace('_', ' ').toUpperCase()}`, 'success');
      
      setTimeout(() => {
        location.reload();
      }, 800);
    } else {
      const errData = await response.json().catch(() => ({}));
      showAlert('Error', errData.detail || 'Failed to move lead stage.', 'error');
    }
  } catch (err) {
    console.error("Kanban update error:", err);
    showAlert('Connection Error', 'Cannot connect to FastAPI server.', 'error');
  }
}

// Client-side search filter 
function filterKanbanCards() {
  const query = (document.getElementById('kanbanSearch')?.value || '').toLowerCase();
  const cards = document.querySelectorAll('.kanban-card');

  cards.forEach(card => {
    const text = card.innerText.toLowerCase();
    if (text.includes(query)) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
}