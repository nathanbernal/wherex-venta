(function () {
  'use strict';

  $(window).scroll(function () {
    var top = $(document).scrollTop();
    if (top > 50) {
      $('#home > .navbar').removeClass('navbar-transparent');
    } else {
      $('#home > .navbar').addClass('navbar-transparent');
    }
  })

  $('a[href="#"]').click(function (event) {
    event.preventDefault();
  })

  $('.bs-component').each(function () {
    var $component = $(this);
    var $button = $('<button class="source-button btn btn-primary btn-xs" role="button" tabindex="0">&lt; &gt;</button>');
    $component.append($button);

    if ($component.find('[data-bs-toggle="tooltip"]').length > 0) {
      $component.attr('data-html', $component.html());
    }
  });

  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl)
  })

  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
  })

  var sourceModalElem = document.getElementById('source-modal');
  if (sourceModalElem) {
    var sourceModal = new bootstrap.Modal(document.getElementById('source-modal'));
  }

  $('body').on('click', '.source-button', function (event) {
    event.preventDefault();

    var component = $(this).parent();
    var html = component.attr('data-html') ? component.attr('data-html') : component.html();

    html = cleanSource(html);
    html = Prism.highlight(html, Prism.languages.html, 'html');
    $('#source-modal code').html(html);
    sourceModal.show();
  })

  function cleanSource(html) {
    html = html.replace(/×/g, '&times;')
               .replace(/«/g, '&laquo;')
               .replace(/»/g, '&raquo;')
               .replace(/←/g, '&larr;')
               .replace(/→/g, '&rarr;')

    var lines = html.split(/\n/);

    lines.shift();
    lines.splice(-1, 1);

    var indentSize = lines[0].length - lines[0].trim().length;
    var re = new RegExp(' {' + indentSize + '}');

    lines = lines.map(function (line) {
      if (line.match(re)) {
        line = line.slice(Math.max(0, indentSize));
      }

      return line;
    });

    lines = lines.join('\n');

    return lines;
  }
})();

function erJson() {

  showMessage("No es posible interpretar la respuesta recibida.");

}

function getProductoPrecio(url, productoId, objId) {

  var dataIn=
    {
      productoId : productoId,
    }

  $.post(
    url,
    {
      _token: $('input[name=_token]').val(),
      data : JSON.stringify(dataIn),
    },
    function (data) {

      try {
        var rec=JSON.parse(data);
        $("#" + objId).val(rec.precio);
        $("#cantidad").focus();
      } catch (ex) {
        showMessage(rec.msg);
      }

    }
  )

}

function showMessage(msg) {

  $("#modal-message-body").html(msg);
  $("#modal-message").modal("show");

}

function onlyNum(e) {

  var n="1234567890"+String.fromCharCode(8)+
    String.fromCharCode(9) +
    String.fromCharCode(96)+
    String.fromCharCode(97) +
    String.fromCharCode(98) +
    String.fromCharCode(99) +
    String.fromCharCode(100) +
    String.fromCharCode(101) +
    String.fromCharCode(102) +
    String.fromCharCode(103) +
    String.fromCharCode(104) +
    String.fromCharCode(105);
  var k=(e.swish)?e.swish:e.keyCode;

  return n.indexOf(String.fromCharCode(k))>-1;

}

/*function showTotal2(precio, cantidad, objTotal) {

  var total=parseInt(precio)*parseInt(cantidad);
  $("#"+objTotal).val(total);

}*/

function setItem(url, productoId, cantidad, ventaId) {

  $.post(
    url,
    {
      _token : $("input[name=_token]").val(),
      productoId: productoId,
      cantidad : cantidad,
      ventaId : ventaId,
    },
    function (data) {

      try {

        var rec=JSON.parse(data);

        if (rec.status=="success") {
          $("#modal-detalle").modal("hide");
          $("#btn_" + ventaId).trigger("click");
        }

      } catch (ex) {

        erJson();
        
      }

    }
  );

}

function setVentaProducto(url, tblId) {

  if (
    $.trim($("#cantidad").val())=="" ||
    $.trim($("#cantidad").val())=="0" ||
    $("#productoId").val()=="0") {

    showMessage("La selecci&oacute;n de producto y el registro de cantidad son obligatorios para agregar el producto.");
    return;

  }

  var dataIn=
    {
      productoId : $("#productoId").val(),
      precio : $("#precio").val(),
      cantidad : $("#cantidad").val(),
      total : $("#total").val(),
    };

  $.post(
    url,
    {
      _token : $("input[name=_token]").val(),
      data : JSON.stringify(dataIn),
    },
    function (data) {

      console.log(data);

      try {

        var rec=JSON.parse(data);
        if (rec.status=="success") {
          $("#"+tblId).html(rec.html);
          $("#totalVenta").val(rec.total);
          $("#totalOff").val(rec.total);
          $("#iva").val(rec.iva);

          showTotal();

        } else {
          showMessage(rec.msg);
        }

      } catch (ex) {

        erJson();

      }

    }
  );

}

function unsetItem(url, itemId, objId) {

  var dataIn=
    {
      itemId : itemId,
    };

  $.post(
    url,
    {
      _token : $("input[name=_token]").val(),
      data : JSON.stringify(dataIn),
    },
    function (data) {

      console.log(data);

      try {

        var rec=JSON.parse(data);
        if (rec.status=="success") {
          $("#"+objId).html(rec.html);
          $("#totalVenta").val(rec.total);
          $("#totalOff").val(rec.total);
          $("#iva").val(rec.iva);
          
          showTotal();

        } else {
          showMessage(rec.msg);
        }

      } catch (ex) {

        erJson();

      }

    }
  );

}


function setVenta(url) {

  var dataIn=
    {
      clienteId : $("#clienteId").val(),
      descuento : $("#descuento").val(),
    };

  $.post(
    url,
    {
      _token : $("input[name=_token]").val(),
      data : JSON.stringify(dataIn),
    },
    function (data) {

      try {

        var rec=JSON.parse(data);
        if (rec.status=="success") {
          showMessage(rec.msg+"<br><small></small>Redireccionando en 2 segundos...</small>");
          setTimeout('$("#form-venta").prop("method", "get"); $("#form-venta").submit();', 2000);
        } else {
          showMessage(rec.msg);
        }

      } catch (ex) {

        erJson();

      }

    }
  );

}

function showTotal() {

  if ($("#descuento").val()>100) {
    showMessage("El descuento no puede exceder el 100%");
    $("#descuento").val("0");
  }

  var desc=($.trim($("#descuento").val())!="")?parseInt($("#descuento").val()):0;
  var tot=parseInt($("#totalOff").val());
  var p=(tot*desc)/100;
  var t2=tot-p;
  $("#totalVenta").val(parseInt(t2));
  var porc19=t2/1.19;
  var iva=t2-porc19;
  $("#iva").val(parseInt(iva));

}


function showVentaDetalle(url) {

  $("#modal-detalle-body").load(
    url,
    {
      _token: $("input[name=_token]").val(),
    },
    function (data) {
      $("#modal-detalle").modal("show");
    }
  );

}

function setDetalleCantidad(url, ventaId, cantidad) {

  $.post(
    url,
    {
      _token: $("input[name=_token]").val(),
      cantidad : cantidad,
    },
    function (data) {

      try {

        var rec=JSON.parse(data);
        if (rec.status=="success") {
          showVentaDetalle($('#urlVentaDetalle').val()+'/'+ventaId);
        }

      } catch (ex) {

        //
      }

    }
  );

}


function unsetVenta(url) {

  $.post(
    url,
    {
      _token: $("input[name=_token]").val()
    },
    function (data) {

      try {

        var rec = JSON.parse(data);

        if (rec.status == "success") {

          $("#modal-detalle").modal("hide");
          showMessage(rec.msg);
          setTimeout('$("#form-reporte").submit();', 2000);

        } else {

          showMessage(rec.msg);

        }

      } catch (ex) {

        erJson();

      }

    }

  );

}

function unsetVentaProducto(url) {

  $.post(
    url,
    {
      _token: $("input[name=_token]").val()
    },
    function (data) {

      try {

        var rec = JSON.parse(data);

        if (rec.status == "success") {

          $("#modal-detalle").modal("hide");
          $("#btn_"+rec.venta_id).trigger("click");

        } else {

          showMessage(rec.msg);

        }

      } catch (ex) {

        erJson();

      }

    }

  );

}
