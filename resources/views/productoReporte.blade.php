@include('inc/header')

    <div class="container">
      <div class="page-header" id="banner">
        <div class="row">
          <div class="col-lg-8 col-md-7 col-sm-6">
            <h1>Productos</h1>
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
                    <td>Id</td>
                    <td>Nombre</td>
                    <td>Cantidad</td>
                  </tr>
                  <tbody id="itemsList">
                    @foreach($producto as $p)
                    <tr>
                      <td>
                        #{{ $p->producto_id }}
                      </td>
                      <td>{{ $p->nombre }}</td>
                      <td>{{ $p->cantidad }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>

              </form>

            </div>

          </div>

        </div>
      </div>

@include('inc/modal')

@include('inc/footer')
