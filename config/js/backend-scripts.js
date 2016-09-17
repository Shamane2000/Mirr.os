/**
 * Scripts for dynamic backend management tasks in the config screen.
 */

$(document).ready(function() {
    $.each(modulesInUse, function(index, value) {
        $('.module-' + value).hide();
    });
});

// Remove red border on input fields when they have focus, since the user might input missing data now.
$('input[type="text"]').focus(function() {
    $(this).removeClass('error');
});

$('.block__actions--edit').click(function() {
    $('#size-row').val($(this).prev().val());
    $('#layout').val(modules[$(this).prev().val()].length);
});

// Reorder active modules when user changes a row layout.
$('#layout').change(function() {
    var row = $('#size-row').val();
    var cols = $(this).val();

    if(cols == 1) {
        if(modules[row][1].length > 0) {
            modulesInUse = $.grep(modulesInUse, function(value) {
                return value != modules[row][1];
            });
            $('.module-' + modules[row][1]).show();
        }
        modules[row].splice(1, 1);

        $('.module-row:eq(' + row + ')').find('.small-6:eq(1)').remove();
        $('.module-row:eq(' + row + ')').find('.small-6:eq(0)').removeClass('small-6').addClass('small-12');
    } else {
        modules[row][1] = '';
        $('.module-row:eq(' + row + ') > .small-12:eq(0)').find('.small-12:eq(0)').removeClass('small-12').addClass('small-6');
        $('.module-row:eq(' + row + ') > .small-12:eq(0) > .row > .small-6:eq(0)').after('<div class="small-6 columns"><div class="block__add">'
            + '<button class="block__add--button" href="#" aria-label="plus button" data-open="gr-modal-add">'
            + '<span class="fi-plus"></span></button></div></div>');
    }

    $.post('writeLayout.php', {'row' : row, 'modules[]': modules[row]})
        .done(function() {
            $('#gr-modal-size').foundation('close');
        });
});

$('body').delegate('.block__add--button', 'click', function() {
    var col;
    if($(this).parent().parent().hasClass('small-12') || $(this).parent().parent().next().hasClass('small-6')) {
        col = 0;
    } else {
        col = 1;
    }
    var row = $(this).parent().parent().parent().parent().children().eq(0).children().eq(0).val();
    $("#newModulCol").val(col);
    $("#newModulRow").val(row);
});

$('body').delegate('.module__delete', 'click', function() {
    var col;
    if($(this).parent().parent().parent().hasClass('small-12') || $(this).parent().parent().parent().next().hasClass('small-6')) {
        col = 0;
    } else {
        col= 1;
    }
    var row = $(this).parent().parent().parent().parent().parent().children().eq(0).children().eq(0).val();
    var moduleId = modules[row][col];

    modules[row][col] = '';
    $.post('writeLayout.php', {'row' : row, 'modules[]': modules[row]});

    $(this).parent().parent().html('<button class="block__add--button" href="#" aria-label="plus button" data-open="gr-modal-add">'
        + '<span class="fi-plus"></span></button>');

    modulesInUse = $.grep(modulesInUse, function(value) {
        return value != moduleId;
    });
    $('.module-' + moduleId).show();
});


// Adds a chosen module to the selected slot.
$('.chooseModule').click(function() {
    var row = $("#newModulRow").val();
    var col = $("#newModulCol").val();
    var moduleId = $(this).children('.newModulId').val();

    modules[row][col] = moduleId;

    $('.module-row').eq(row).children().eq(0).children().eq(1).children().eq(col).children().eq(0).html(
        '<div class="module">' +
        '    <button class="module__edit" data-open="gr-modal-' + moduleId + '">' +
        '        <span class="fi-pencil"></span>' +
        '    </button>' +
        '    <span class="module__title">' + moduleNames[moduleId] + '</span>' +
        '    <button class="module__delete" href="#">' +
        '        <span class="fi-trash"></span>' +
        '    </button>' +
        '</div>');

    modulesInUse.push(moduleId);
    $('.module-' + moduleId).hide();

    $.post('writeLayout.php', {'row' : row, 'modules[]': modules[row]})
        .done(function() {
            $('#gr-modal-add').foundation('close');
            return false;
        });
});

// Respond to a module deletion intent: user taps trash icon in module library.
$('[data-deleteModule]').click(function(e) {
    e.preventDefault();
    moduleName = $(this).attr('data-deleteModule');
    console.log("user wants to delete " + moduleName);
    $('[data-deleteModule-confirmbox=' + moduleName +']').show();
});

$('[data-deleteModule-confirm]').click(function() {
    moduleName = $(this).attr('data-deleteModule-confirm');
    var row = $('section.module-' + moduleName);

    $.ajax({
        url: "deleteModule.php",
        type: "POST",
        data: {module: moduleName, action: 'delete'},
        success: function(response) {
            row.html("<p>" + LOCALE.deleteSuccess + "</p>");
            row.fadeOut(3000);
        },
        error: function(jqXHR, textStatus, errorMessage) {
            console.log(jqXHR.responseText);
            $('#fileError').text(LOCALE.internalError + jqXHR.responseText).animate({
                opacity: 0
            }, 6000, function () {
                $('#fileError').text('');
                $('#fileError').css('opacity', 1);
            })
        }
    });
});

$('[data-deleteModule-cancel]').click(function() {
    moduleName = $(this).attr('data-deleteModule-cancel');
    console.log("user has canceled deletion of " + moduleName);
    $('[data-deleteModule-confirmbox=' + moduleName +']').hide();
});

//@TODO: Closes the deletion confirmation box if user closes the modal before.
$('#gr-modal-add').on('closed.zf.reveal', function(){
    $('[data-deleteModule-confirmbox]').hide();
});

/**
 * Install an uploaded module
 * @param moduleName
 * @param moduleVersion
 */
function installModule(moduleName, moduleVersion) {
    $.ajax({
        url: "installModule.php",
        type: "POST",
        data: {name: moduleName, version: moduleVersion},
        success: function(response) {
            console.log(response);
            location.reload();
        }
    });
}

function openZipUpload() {
    $('#moduleZip').click();
}

var zipInput = $('#moduleZip'),
    uploadButton = $('#uploadModule');

zipInput.change(function() {
    uploadModule = "";
    uploadButton.attr("disabled", true);
    uploadButton.addClass('loading__button');
    var initialText = uploadButton.text();
    uploadButton.html('<div class="loading__button--inner"></div>');

    var file = zipInput[0].files[0];
    if(file.type != 'application/zip') {
        zipInput.val('');
        $('#fileError').text(LOCALE.notZip).animate({
            opacity: 0
        }, 3000, function() {
            $('#fileError').text('');
            $('#fileError').css('opacity',1);
        });

    } else {
        var fd = new FormData();
        fd.append("moduleZip", file);

        $.ajax({
            url: "checkModuleZip.php",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            success: function(response) {
                uploadModule = response.split(': ')[0];
                console.log('"' + uploadModule + '"');
            },
            error: function(jqXHR, textStatus, errorMessage) {
                console.log(errorMessage); // Optional
            }
        });
    }
    window.setTimeout(
        function() {
            uploadButton.attr("disabled", false);
            uploadButton.html(initialText);
            uploadButton.removeClass('loading__button');
        },
        3000
    )
});