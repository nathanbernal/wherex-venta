@include('inc/header')

    <div class="container">
      <div class="page-header" id="banner">
        <div class="row">
          <div class="col-lg-8 col-md-7 col-sm-6">
            <h1>Ventas</h1>
            <p class="lead">Gesti&oacute;n de ventas</p>
          </div>
        </div>
      </div>

      <!-- Forms
      ================================================== -->
      <div class="bs-docs-section">

        <div class="row">

          <div class="col-lg-12">

            <div class="">

              <form id="form-venta" name="form-venta" method="post" action="{{ url('/ventaReporte') }}">

                @csrf

                <fieldset>
                  <legend>Registro de ventas</legend>
                  <div class="form-group">
                    <label for="clienteId" class="form-label mt-4">Cliente</label>
                    <select class="form-select" id="clienteId" name="clienteId">
                      @foreach($cliente as $c)
                      <option value="{{ $c->cliente_id }}">{{ $c->nombre }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="productoId" class="form-label mt-4">Producto</label>
                    <select class="form-select" id="productoId" name="productoId" onchange="getProductoPrecio('{{ url('/ajax/producto') }}', $(this).val(), 'precio')">
                      <option value="0">SELECCIONE PRODUCTO</option>
                      @foreach($producto as $p)
                      <option value="{{ $p->producto_id }}">{{ $p->nombre }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="col-form-label mt-4" for="precio">Precio</label>
                    <input type="text" class="form-control" placeholder="0" id="precio" name="precio" disabled>
                  </div>

                  <div class="form-group">
                    <label class="col-form-label mt-4" for="cantidad">Cantidad</label>
                    <input type="number" class="form-control" placeholder="0" id="cantidad" name="cantidad" onkeyup="if (onlyNum(event)) { showTotal($('#precio').val(), $('#cantidad').val(), 'total'); return true; } else { return false; }" onfocus="this.select();">
                  </div>

                  <br>
                  <button type="button" class="btn btn-primary" onclick="setVentaProducto('{{ url('/ajax/productoItem') }}', 'itemsList');">Agregar producto</button>

                </div>
              </form>

            </div>

          </div>

          <div class="col-lg-12">

            <div class="">

              <table class="table table-bordered table-hover table-striped">
                <tr>
                  <td>#</td>
                  <td>Nombre</td>
                  <td>Precio</td>
                  <td>Cantidad</td>
                  <td>Total</td>
                  <td>&nbsp;</td>
                </tr>
                <tbody id="itemsList">
                  {!! $html !!}
                </tbody>
              </table>

              Aplicar descuento(%):
              <input type="number" maxlength="3" class="form-control" placeholder="0" id="descuento" name="descuento" onkeydown="return onlyNum(event);" onkeyup="showTotal();" onfocus="this.select();">
              <br>
              Total:
              <input type="number" maxlength="3" class="form-control" placeholder="0" id="totalVenta" name="totalVenta" readonly value="{{ $total }}">
              <input type="hidden" id="totalOff" name="totalOff" value="{{ $total }}">
              Iva:
              <input type="number" maxlength="3" class="form-control" placeholder="0" id="iva" name="iva" readonly value="{{ $iva }}">

              <button type="button" class="btn btn-warning" onclick="setVenta('{{ url('/ajax/setVenta') }}');">Registrar venta</button>

              <input type="hidden" id="actionUnset" value="{{ url('/ajax/productoItemUnset') }}">

            </div>

          </div>

        </div>
      </div>

@include('inc/modal')

@include('inc/footer')
