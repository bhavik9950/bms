import './bootstrap';
import '../css/app.css';
import './toggle-password';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swal from 'sweetalert2';
import mask from '@alpinejs/mask';
// jQuery + DataTables
import $ from 'jquery';
import DataTable from 'datatables.net';
import 'datatables.net-dt/css/dataTables.dataTables.css';
// import 'datatables.net-bs5/css/dataTables.bootstrap5.css';


// Custom scripts
import './main.js';
import './importExcel.js';
import './garments.js';
import './delete.js';
import './measurements.js';
import './fabric.js';
import './relations.js';
import './modals.js';
import './item-card.js';
import './fetch-garment.js';
import './audio.js';
import './roles.js';
import './staff.js';
import './salary.js';

// Alpine setup
Alpine.plugin(collapse);
Alpine.plugin(mask);
window.Alpine = Alpine;
Alpine.start();

// SweetAlert2 global
window.Swal = Swal;


document.addEventListener("DOMContentLoaded", function () {
    $('#garments-table').DataTable({
        responsive: true,
    });

    $('#measurements-table').DataTable({
        responsive: true,
    });
    $('#garment-measurement-table').DataTable({
        responsive: true,
    });
    $('#fabric-table').DataTable({
        responsive: true,
    });
    $('#roles-table').DataTable({
        responsive: true,
    });
    $('#staff-table').DataTable({
        responsive: true,
    });
});
