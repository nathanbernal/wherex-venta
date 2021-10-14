
    <div class="container">
        <div class="row">
          <div class="col-lg-8 col-md-7 col-sm-6">
            <h1>Venta #{{ $venta->venta_id }}</h1>
            <h3>Cliente {{ $venta->cliente_nombre }}</h3>
          </div>

          <div class="col-lg-12">

            <div class="">

              <p class="lead">Agregar producto</p>

              <table>
                <tr>
                  <td>
                    <select id="productoIdDetalle" class="form-control" onchange="getProductoPrecio('{{ url('/ajax/producto') }}', $(this).val(), 'precioDetalle');">
                      <option value="0">SELECCIONE PRODUCTO</option>
                    @foreach($producto as $p)
                      <option value="{{ $p->producto_id }}">{{ $p->nombre}}</option>
                    @endforeach
                    </select>
                  </td>
                  <td>
                    <input type="text" class="form-control" id="precioDetalle" readonly>
                  </td>
                  <td>
                    <input type="text" class="form-control" id="cantidad" onkeydown="return onlyNum(event);">
                  </td>
                  <td>
                    <button class="btn btn-sm btn-primary" type="button" onclick="setItem('{{ url('/ajax/setDetalleItem') }}', $('#productoIdDetalle').val(), $('#cantidad').val(), '{{ $venta->venta_id}}');">Agregar</button>
                  </td>
                </tr>
              </table>

              <p class="lead">Detalle de la venta</p>

              <table width="100%" class="table table-bordered table-hover">
                <tr>
                  <td>#</td>
                  <td>NOMBRE</td>
                  <td>CANTIDAD</td>
                  <td>SUB TOTAL</td>
                  <td width="1%"></td>
                </tr>
                @foreach($detalle as $d)
                <tr>
                  <td>#{{ $d->detalle_id }}</td>
                  <td>{{ $d->producto_nombre }}</td>
                  <td width="20%">{{ $d->cantidad }}</td>
                  <td>${{ $d->subtotal }}</td>
                  <td width="1%">
                    
                    <button type="button" class="btn btn-danger" onclick="if (confirm('¿Está seguro de eliminar el producto seleccionado?')) { unsetVentaProducto('{{ url('/ajax/unsetVentaProducto') }}/{{ $d->detalle_id }}'); }" @if (count($detalle)==1) disabled @endif >Eliminar</button>
                  </td>
                </tr>
                @endforeach
              </table>

              <h5>Sub Total ${{ $venta->total }}</h5>
              <h5>Descuento {{ $venta->descuento }}%</h5>
              <h3>Total ${{ ceil($venta->total-(($venta->total*$venta->descuento)/100)) }}</h3>
            </div>

          </div>

      </div>
