document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

    const roleRadios = document.querySelectorAll('input[name="role"]');
    roleRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            handleRoleChange(this.value);
        });
    });

    const selectedRole = document.querySelector('input[name="role"]:checked');
    if (selectedRole) {
        handleRoleChange(selectedRole.value);
    }

    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');

    if (photoInput && photoPreview) {
        photoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    photoPreview.src = e.target.result;
                    photoPreview.classList.add('show');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (typeof google !== 'undefined' && document.getElementById('map')) {
        initMap();
    }
});

function handleRoleChange(role) {
    document.querySelectorAll('.conditional-fields').forEach(el => {
        el.classList.remove('show');
    });

    if (role === 'gerente' || role === 'super_usuario') {
        const adminAuthFields = document.getElementById('adminAuthFields');
        if (adminAuthFields) {
            adminAuthFields.classList.add('show');
        }
    } else if (role === 'proveedor') {
        const proveedorFields = document.getElementById('proveedorFields');
        if (proveedorFields) {
            proveedorFields.classList.add('show');
        }
        if (typeof google !== 'undefined' && !window.mapInitialized) {
            initMap();
        }
    }
}

let map;
let marker;
let geocoder;

function initMap() {
    const defaultLat = parseFloat(document.getElementById('latitud')?.value) || 4.7110;
    const defaultLng = parseFloat(document.getElementById('longitud')?.value) || -74.0721;

    const defaultLocation = { lat: defaultLat, lng: defaultLng };

    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: defaultLocation,
        mapTypeControl: false,
        streetViewControl: false,
        fullscreenControl: true,
    });

    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
    });

    geocoder = new google.maps.Geocoder();

    marker.addListener('dragend', function () {
        const position = marker.getPosition();
        updateCoordinates(position.lat(), position.lng());
        reverseGeocode(position);
    });

    map.addListener('click', function (e) {
        marker.setPosition(e.latLng);
        updateCoordinates(e.latLng.lat(), e.latLng.lng());
        reverseGeocode(e.latLng);
    });

    const addressInput = document.getElementById('direccion_proveedor');
    if (addressInput) {
        addressInput.addEventListener('blur', function () {
            geocodeAddress(this.value);
        });
    }

    window.mapInitialized = true;
}

function updateCoordinates(lat, lng) {
    const latInput = document.getElementById('latitud');
    const lngInput = document.getElementById('longitud');

    if (latInput) latInput.value = lat.toFixed(8);
    if (lngInput) lngInput.value = lng.toFixed(8);
}

function geocodeAddress(address) {
    if (!address || !geocoder) return;

    geocoder.geocode({ address: address }, function (results, status) {
        if (status === 'OK') {
            const location = results[0].geometry.location;
            map.setCenter(location);
            marker.setPosition(location);
            updateCoordinates(location.lat(), location.lng());

            const addressComponents = results[0].address_components;
            extractLocationDetails(addressComponents);
        }
    });
}

function reverseGeocode(location) {
    if (!geocoder) return;

    geocoder.geocode({ location: location }, function (results, status) {
        if (status === 'OK' && results[0]) {
            const addressComponents = results[0].address_components;
            extractLocationDetails(addressComponents);
        }
    });
}

function extractLocationDetails(addressComponents) {
    let city = '';
    let country = '';

    addressComponents.forEach(component => {
        if (component.types.includes('locality')) {
            city = component.long_name;
        }
        if (component.types.includes('country')) {
            country = component.long_name;
        }
    });

    const ciudadInput = document.getElementById('ciudad');
    const paisInput = document.getElementById('pais');

    if (ciudadInput && city) ciudadInput.value = city;
    if (paisInput && country) paisInput.value = country;
}

function confirmDelete(userName) {
    return confirm(`¿Estás seguro de que deseas eliminar al usuario "${userName}"? Esta acción no se puede deshacer.`);
}

const gerenteForm = document.getElementById('userForm');
if (gerenteForm) {
    gerenteForm.addEventListener('submit', function (e) {
        const roleInput = document.querySelector('input[name="role"]:checked');
        const adminPasswordInput = document.getElementById('admin_password');

        if (roleInput && (roleInput.value === 'gerente' || roleInput.value === 'super_usuario') && adminPasswordInput) {
            if (!adminPasswordInput.value) {
                e.preventDefault();
                alert('Debes ingresar la contraseña de autorización para registrarte con este rol.');
                adminPasswordInput.focus();
                return false;
            }
        }
    });
}
