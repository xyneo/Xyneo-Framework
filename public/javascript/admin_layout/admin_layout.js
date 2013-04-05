$(document).ready(
  function()
  {
      var $base_url = $('base:first').attr('href');
      $Functions = new Functions($base_url);
      $Functions.__construct();
      $(window).unload(
        function()
        {
            delete $Functions;
            delete $base_url;
        }
      );
  }
);

//VAR privát érték/funkció: egyszerű hivatkozás; this publikus érték/funkció: hivatkozás '$Functions.valami' vagy '$Functions.valami()'.
function Functions($base_url)
{
    var ajaxLoader = null;

    this.__construct = function()
    {
        //Footer igazítása
        $height = $(window).height() - $('.xfw_content').offset().top - $('.xfw_footline').height() * 2 - 3;
        if ($height < $('.xfw_sidebar').outerHeight())
          $height = $('.xfw_sidebar').outerHeight();
        $('.xfw_content').css('min-height', $height +'px');
        $(window).resize(
          function()
          {
              $height = $(window).height() - $('.xfw_content').offset().top - $('.xfw_footline').height() * 2 - 3;
              if ($height < $('.xfw_sidebar').outerHeight())
                $height = $('.xfw_sidebar').outerHeight();
              $('.xfw_content').css('min-height', $height +'px');
          }
        );

        //Oldalmenü animált háttere
        $('.xfw_sidemenu li').hover(
          function()
          {
              $(this).stop().animate(
                {
                    backgroundPosition: '0px 0px'
                }, 300
              );
          },
          function()
          {
              $(this).stop().animate(
                {
                    backgroundPosition: '-333px 0px'
                }, 300
              );
          }
        );

        //CKEDITOR beállítása
        $('textarea').each(
          function()
          {
              if ($(this).parent().get(0).tagName != 'TD')
                CKEDITOR.replace($(this).attr('name'));
          }
        );

        //Livesearch beállítása
        $('#search').keyup(
          function(e)
          {
              if (e.keyCode == 37 || e.keyCode == 38 || e.keyCode == 39 || e.keyCode == 40 || e.keyCode == 9 || e.keyCode == 27 || e.keyCode == 13)
                return;
              else
              {
                if (ajaxLoader != null)
                  ajaxLoader.abort();
                ajaxLoader = $.ajax(
                  {
                      type: 'POST',
                      url: $('#uri').length == 1 ? $('#uri').val() : $base_url + location.href.match(/a_([a-z]+)/g) +'\/kereses' + (location.href.match(/\/oldal\/\d+/g) != null ? '\/'+ location.href.replace(/\D/g, '') : '\/1'),
                      data:
                      {
                        search: $('#search').val().length > 2 ? $('#search').val() : ''
                      },
                      success: function($answer)
                      {
                          $('#results').html($answer);
                      }
                  }
                );
              }
          }
        );

        //Csak számokat tartalmazó mezők beállítása
        $('.onlyNumbers').keydown(
          function(e)
          {
              if (e.keyCode == 46 || e.keyCode == 8 || e.keyCode == 9 || e.keyCode == 27 || e.keyCode == 13 || (e.keyCode == 65 && e.ctrlKey === true) || (e.keyCode >= 35 && e.keyCode <= 39))
                return;
              else {
                if (e.shiftKey || (e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105))
                  e.preventDefault();
              }
          }
        );
    }
}