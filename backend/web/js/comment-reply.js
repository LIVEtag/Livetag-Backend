$(function () {
    clearParentComment();

    // If seller clicks on reply button - show parent comment block with comment info in the form
    $('#comments').on('click', '.comment-reply', function () {
        $('.parent-comment-reply').css('display', 'block');
        clearParentComment();
        row = $(this).parents('tr');
        id = row.data('key');
        $('#commentform-parentcommentid').val(id);
        if (row.hasOwnProperty(0)) {
            tr = row[0];
            cells = tr.cells;
            if (cells.hasOwnProperty(0)) {
                idCell = cells[0];
                $('.parent-comment-id').append(idCell.textContent);
            }
            if (cells.hasOwnProperty(1)) {
                nameCell = cells[1];
                $('.parent-comment-name').append(nameCell.textContent);
            }
            if (cells.hasOwnProperty(2)) {
                textCell = cells[2];
                $('.parent-comment-text').append(textCell.textContent);
            }
            if (cells.hasOwnProperty(3)) {
                dateTimeCell = cells[3];
                $('.parent-comment-date-time').append(dateTimeCell.textContent);
            }
        }
    });

    // Hide parent comment block in the form by click on the close button
    $('.parent-comment-reply .close').on('click', function () {
        hideParentComment();
    });

    // If seller disables comments - hide parent comment block in the form
    $('.comments-content').on('click', 'button', function () {
        hideParentComment();
    });

    // If seller wants to delete comment - hide this parent comment block in the form
    $('#comments').on('click', '.glyphicon-trash', function () {
        row = $(this).parents('tr');
        idFromGrid = row.data('key');
        idFromInput = $('#commentform-parentcommentid').val();
        if (idFromGrid == idFromInput) {
            hideParentComment();
        }
    });

    function clearParentComment() {
        $('.parent-comment-reply .parent-comment').empty();
        $('#commentform-parentcommentid').val('');
    }

    function hideParentComment() {
        $('.parent-comment-reply').hide();
        clearParentComment();
    }
});