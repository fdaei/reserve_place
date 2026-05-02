import $ from 'jquery'
import 'bootstrap-v4-rtl/dist/js/bootstrap.min.js'
import 'bootstrap-v4-rtl/dist/css/bootstrap-rtl.min.css'
import 'lottie-web/build/player/lottie.js'
import 'font-awesome-4/css/font-awesome.min.css'
import 'jquery-ui';
import Swal from 'sweetalert2'
import { initAdminFormTools } from './admin-form-tools'

window.$ = window.jQuery = $;
window.Swal = Swal;

initAdminFormTools();

function initAdminSidebarAccordion() {
    document.querySelectorAll('[data-admin-menu-section]').forEach((section) => {
        if (section.dataset.bound === 'true') {
            return;
        }

        section.addEventListener('toggle', () => {
            if (!section.open) {
                return;
            }

            document.querySelectorAll('[data-admin-menu-section]').forEach((otherSection) => {
                if (otherSection !== section) {
                    otherSection.open = false;
                }
            });
        });

        section.dataset.bound = 'true';
    });
}

function initConfirmableForms() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        if (form.dataset.bound === 'true') {
            return;
        }

        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === 'true') {
                return;
            }

            event.preventDefault();

            Swal.fire({
                title: form.dataset.confirmTitle || 'تأیید عملیات',
                text: form.dataset.confirm || 'از انجام این عملیات اطمینان دارید؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: form.dataset.confirmButton || 'تأیید',
                cancelButtonText: 'انصراف',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        });

        form.dataset.bound = 'true';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminSidebarAccordion();
    initConfirmableForms();
});

let failedlottie = document.getElementById("failed-lottie")
let successlottie = document.getElementById("success-lottie")

function successfullymodal(text) {
    $("#succes-dialog .modal-body p").html(text)
    $("#succes-dialog").modal("show")
    successlottie?.play()
}

function failedmodal(text) {
    $("#failed-dialog .modal-body p").html(text)
    $("#failed-dialog").modal("show")
    failedlottie?.play()
}

$('#succes-dialog').on('hidden.bs.modal', function () {
    successlottie?.stop()
})
$('#failed-dialog').on('hidden.bs.modal', function () {
    failedlottie?.stop()
})

function isemail(email) {
    var regex = /^([a-za-z0-9_.+-])+\@(([a-za-z0-9-])+\.)+([a-za-z0-9]{2,4})+$/;
    return regex.test(email);
}

let isloading = false;

function loadingbutton(btn) {
    btn.find("i.fa").remove()
    btn.append('<i class="fa fa-spinner fa-spin" style="margin-left: 12px"></i>')
    isloading = true
}

function unsetloadingbutton(btn) {
    btn.find("i.fa").remove()
    isloading = false
}

function redirect(page) {
    window.location.replace(page)
}

$("#hide-failed-modal-btn").click(function () {
    $("#failed-dialog").modal("hide")
})

var numberformat = (price) => price.toLocaleString('en-US');

window.successfullymodal = successfullymodal;
window.failedmodal = failedmodal;
window.loadingbutton = loadingbutton;
window.unsetloadingbutton = unsetloadingbutton;
window.redirect = redirect;
window.numberformat = numberformat;
window.isemail = isemail;
