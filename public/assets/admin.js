(function($) {

  function init() {
    $('[data-action="delete"]').on('click', function(e) {
      e.preventDefault();

      if (confirm('Are you sure?')) {
        window.location.href = $(this).prop('href');
      }
    });
  }

  init();
})(jQuery);