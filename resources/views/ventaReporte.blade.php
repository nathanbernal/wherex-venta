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

      <div class="bs-docs-section">

        <div class="row">

          <div class="col-lg-12">

            <div class="">

              <form id="form-reporte" name="form-reporte" method="get" action="{{ url('/ventaReporte') }}">

                @csrf

                <table class="table table-bordered table-hover table-striped">
                  <tr>
                    <td>Fecha</td>
                    <td>Cliente</td>
                    <td>Total</td>
                    <td>IVA</td>
                    <td>Descuento</td>
                    <td>Total</td>
                    <td>Detalle</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tbody id="itemsList">
                    @foreach($venta as $v)
                    <tr>
                      <td>
                        #{{ $v->venta_id }}
                      </td>
                      <td>{{ $v->cliente_nombre }}</td>
                      <td>${{ number_format($v->total, 0, ',', '.') }}</td>
                      <td>${{ $v->iva }}</td>
                      <td>{{ $v->descuento }}%</td>
                      <td>${{ number_format($v->total-(($v->total*$v->descuento)/100), 0, ',', '.') }}</td>
                      <td>
                        @if ($v->detalle_cantidad>0)
                        <button type="button" id="btn_{{ $v->venta_id }}" class="btn btn-primary" onclick="showVentaDetalle('{{ url('/ventaDetalle') }}/{{ $v->venta_id }}');">Detalle #{{ $v->venta_id }}</button>
                        @else
                          <span class="label label-danger">Venta sin detalle asociado.</span>
                        @endif
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="if (confirm('¿Está seguro de proceder con la eliminación de la venta?')) { unsetVenta('{{ url('/ajax/unsetVenta') }}/{{ $v->venta_id }}'); }" >Eliminar venta</button>                        
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>

                <input type="hidden" id="urlVentaDetalle" value="{{ url('/ventaDetalle') }}">

              </form>

            </div>

          </div>

        </div>
      </div>

@include('inc/modal')

@include('inc/footer')
