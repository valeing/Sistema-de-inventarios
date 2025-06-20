document.getElementById('buscarResguardante').addEventListener('input', function() {
    const query = this.value;
    const idResguardanteInput = document.getElementById('id_resguardante');
    const mensajeResguardante = document.getElementById('mensajeResguardante');

    if (query.length > 1) {
        fetch(`/buscar-resguardantes?query=${query}`)
            .then(response => response.json())
            .then(data => {
                const resultados = document.getElementById('resguardanteResultados');
                resultados.innerHTML = '';
                if (data.length > 0) {
                    const maxResults = 4;
                    data.slice(0, maxResults).forEach(resguardante => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.classList.add('list-group-item', 'list-group-item-action',
                            'd-flex', 'justify-content-between', 'align-items-center');
                        item.innerHTML =
                            `<span><strong>${resguardante.nombre_apellido}</strong><br><small>N° de personal: ${resguardante.numero_empleado}</small></span>`;
                        item.addEventListener('click', () => seleccionarResguardante(
                            resguardante));
                        resultados.appendChild(item);
                    });
                    mensajeResguardante.textContent = '';
                } else {
                    mensajeResguardante.textContent = 'Resguardante no encontrado';
                }
            });
    } else {
        idResguardanteInput.value = '';
        mensajeResguardante.textContent = '';
        document.getElementById('resguardanteResultados').innerHTML = '';
    }
});

function seleccionarResguardante(resguardante) {
    document.getElementById('buscarResguardante').value =
        `${resguardante.nombre_apellido} (N° de personal: ${resguardante.numero_empleado})`;
    document.getElementById('id_resguardante').value = resguardante.id_resguardante;
    document.getElementById('resguardanteResultados').innerHTML = '';
    document.getElementById('mensajeResguardante').textContent = '';
}
