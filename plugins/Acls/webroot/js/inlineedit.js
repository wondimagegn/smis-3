// app/webroot/js/example.js
$(function() {
    $('.post').editable('/students/updateEmail', {
         id        : 'data[Student][id]',
         name      : 'data[Student][email]',
         type      : 'text',
         cancel    : 'Cancel',
         submit    : 'Save',
         tooltip   : 'Click to edit the title'
    });
});
