let index = 0;
const wrapper   = document.getElementById('itemWrapper');
const addRowBtn = document.getElementById('addRow');
const submitBtn = document.getElementById('submitBtn');

function selectedIds() {
    return [...document.querySelectorAll('.material-select')].map(s => s.value).filter(Boolean);
}

function refreshOptions() {
    const used = selectedIds();
    document.querySelectorAll('.material-select').forEach(select => {
        const current = select.value;
        select.innerHTML = '<option value="">-- Pilih Material --</option>';
        allMaterials.forEach(m => {
            if (!used.includes(String(m.id)) || String(m.id) === current) {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = `${m.name} (Sisa: ${m.stock})`;
                opt.dataset.stock = m.stock;
                if (String(m.id) === current) opt.selected = true;
                select.appendChild(opt);
            }
        });
    });
}

function addRow() {
    const row = document.createElement('div');
    row.className = 'grid grid-cols-3 gap-3 mb-3 itemRow';

    row.innerHTML = `
        <select name="materials[${index}][material_id]" class="material-select border p-2 rounded" required></select>
        <div>
            <input name="materials[${index}][quantity]" type="number" min="1" class="qty-input border p-2 rounded w-full" placeholder="Qty" required>
            <p class="qty-warning hidden text-xs text-red-600"></p>
        </div>
        <button type="button" class="removeRow bg-red-500 text-white rounded px-3">X</button>
    `;
    wrapper.appendChild(row);
    index++;
    refreshOptions();
}

addRowBtn.onclick = addRow;

document.addEventListener('click', e => {
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('.itemRow').remove();
        refreshOptions();
    }
});

document.addEventListener('change', e => {
    if (e.target.classList.contains('material-select')) refreshOptions();
});
