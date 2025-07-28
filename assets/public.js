document.addEventListener('DOMContentLoaded', function() {
  const projects = window.wcpProjectsData || [];
  const listContainer = document.getElementById('wcp-project-list');
  const statusFilter = document.getElementById('wcp-status-filter');
  const searchInput = document.getElementById('wcp-search-input');
  const noResults = document.getElementById('wcp-no-results-message');

  function render(cards) {
    listContainer.innerHTML = '';
    noResults.style.display = cards.length ? 'none' : 'block';

    cards.forEach(p => {
      const card = document.createElement('div');
      card.className = 'project-card';
      card.innerHTML = `
        <h3>${p.title}</h3>
        <div class="project-meta">
          <span class="project-status status-${p.status}">${p.status_label}</span>
          <span><strong>Start:</strong> ${p.startDate}</span>
          <span><strong>End:</strong> ${p.endDate}</span>
        </div>
        <p class="project-description">${p.description}</p>
      `;
      listContainer.appendChild(card);
    });
  }

  function filterAndRender() {
    let filtered = projects;
    const sel = statusFilter.value;
    if (sel !== 'all') {
      filtered = filtered.filter(p => p.status === sel);
    }
    if (searchInput) {
      const term = searchInput.value.toLowerCase();
      filtered = filtered.filter(p => p.title.toLowerCase().includes(term));
    }
    render(filtered);
  }

  if (statusFilter) statusFilter.addEventListener('change', filterAndRender);
  if (searchInput)  searchInput.addEventListener('input', filterAndRender);

  // Initial render
  render(projects);
});
