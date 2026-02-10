import 'bootstrap-v4-rtl/dist/js/bootstrap.min.js'
import 'bootstrap-v4-rtl/dist/css/bootstrap-rtl.min.css'
import 'lottie-web/build/player/lottie.js'
import 'font-awesome-4/css/font-awesome.min.css'
import 'jquery-ui';
import Swal from 'sweetalert2'

window.Swal=Swal;



let failedlottie = document.getElementById("failed-lottie")
let successlottie = document.getElementById("success-lottie")

function successfullymodal(text) {
    $("#succes-dialog .modal-body p").html(text)
    $("#succes-dialog").modal("show")
    successlottie.play()
}

function failedmodal(text) {
    $("#failed-dialog .modal-body p").html(text)
    $("#failed-dialog").modal("show")
    failedlottie.play()
}

$('#succes-dialog').on('hidden.bs.modal', function () {
    successlottie.stop()
})
$('#failed-dialog').on('hidden.bs.modal', function () {
    failedlottie.stop()
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
    window.location.replace( page
    )
}

$("#hide-failed-modal-btn").click(function () {
    $("#failed-dialog").modal("hide")
})


var numberformat = (price) => price.tolocalestring('en-us');


function numberwithcommas(field) {
    // get the input field id
    let element = document.getelementbyid(field.id);
    // replace all the commas of input field value with empty value
    // and assign it to amount variable
    let amount = element.value.replace(/,/g, '');

    // check if the amount contains (-) sign in the beginning
    if (amount.charat(0) == '-') {
        // if amount contains (-) sign, remove first character then replace all
        // (-) signs from the amount with empty value & concate (-) in amount
        amount = '-' + amount.substring(1).replace(/-/g, '');
    } else {
        // replace all (-) signs from the amount with empty value
        amount = amount.replace(/-/g, '');
    }

    // first add commas into the amount then update the input field value
    element.value =
        amount.tostring().replace(/\b(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
}

$(document).ready(function () {
    // accept only numbers, commas & (-) minus sign
    $(".numbers").on("keypress keyup blur", function (event) {
        $(this).val($(this).val().replace(/[^-\d,]+/g, ""));
        if ((event.which < 48 || event.which > 57) &&
            event.which != 44 && event.which != 45) {
            event.preventdefault();
            return false;
        }
    });
});

