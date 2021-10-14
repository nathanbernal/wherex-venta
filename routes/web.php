<?php

use Illuminate\Support\Facades\Route;


Route::post(
  '/ajax/producto',
  'App\Http\Controllers\AjaxController@producto'
);

Route::post(
  '/ajax/productoItem',
  'App\Http\Controllers\AjaxController@productoItem'
);

Route::post(
  '/ajax/setDetalleItem',
  'App\Http\Controllers\AjaxController@setDetalleItem'
);

Route::post(
  '/ajax/productoItemUnset',
  'App\Http\Controllers\AjaxController@productoItemUnset'
);

Route::post(
  '/ajax/setVenta',
  'App\Http\Controllers\AjaxController@setVenta'
);

Route::post(
  '/ajax/detalleSetCantidad/{detalleId}',
  'App\Http\Controllers\AjaxController@setDetalleCantidad'
);

Route::post(
  '/ajax/unsetVenta/{ventaId}',
  'App\Http\Controllers\AjaxController@unsetVenta'
);

Route::post(
  '/ajax/unsetVentaProducto/{detalleId}',
  'App\Http\Controllers\AjaxController@unsetVentaProducto'
);

Route::get('/', function () {

  $producto=DB::table("vt_producto AS p")
    ->select(
      "p.producto_id",
      "p.nombre",
      "p.cantidad",
      "p.precio")
    ->where("p.cantidad", ">", 0)
    ->orderBy("p.nombre")
    ->get();

  $cliente=DB::table("vt_cliente AS c")
    ->select(
      "c.cliente_id",
      "c.nombre",
      "c.estado_id")
    ->where("c.estado_id", "=", 1)
    ->orderBy("c.nombre")
    ->get();

  session_start();

  $items=[];
  $html='';

  if (isset($_SESSION["items"])) {
    $items=$_SESSION["items"];
  }

  $total=0;

  foreach($items as $k=>$i) {

    $html.='
      <tr>
        <td>'.($k+1).'</td>
        <td>'.$i["nombre"].'</td>
        <td>$'.number_format($i["precio"], 0, ',', '.').'</td>
        <td>'.number_format($i["cantidad"], 0, ',', '.').'</td>
        <td>$'.number_format($i["precio"]*$i["cantidad"], 0, ',', '.').'</td>
      <td>
        <button type="btn btn-danger btn-sm" onclick="unsetItem($(\'#actionUnset\').val(), \''.$k.'\', \'itemsList\');">
          Eliminar
        </button>
      </td>
      </tr>';

    $total+=$i["precio"]*$i["cantidad"];

  }

  return view('principal')->with(
    [
      "producto"=>$producto,
      "cliente"=>$cliente,
      "html"=>$html,
      "total"=>$total,
      "iva"=>ceil($total-($total/1.19))
    ]);

});

Route::get('/ventaReporte', function () {

  $venta=\App\Models\Venta::
    selectRaw(
      "vt_venta.venta_id AS venta_id,
      vt_cliente.nombre AS cliente_nombre,
      vt_venta.total AS total,
      vt_venta.iva AS iva,
      vt_venta.descuento AS descuento,
      (SELECT COUNT(*) FROM vt_detalle WHERE venta_id=vt_venta.venta_id) AS detalle_cantidad")
    ->join("vt_cliente", "vt_venta.cliente_id", "=", "vt_cliente.cliente_id")
    ->get();

  return view('ventaReporte')->with(["venta"=>$venta ]);

});

Route::get('/clienteReporte', function () {

  $cliente=\App\Models\Cliente::selectRaw("vt_cliente.cliente_id, vt_cliente.nombre, vt_cliente_estado.nombre AS estado_nombre")
    ->join("vt_cliente_estado", "vt_cliente_estado.cliente_estado_id", "=", "vt_cliente.estado_id")
    ->get();

  return view('clienteReporte')->with(["cliente"=>$cliente ]);

});

Route::get('/productoReporte', function () {

  $producto=\App\Models\Producto::all();

  return view('productoReporte')->with(["producto"=>$producto]);

});


Route::post('/ventaDetalle/{ventaId}', function (Request $req, $ventaId = null) {

  $venta=\App\Models\Venta::
    selectRaw(
      "vt_venta.venta_id AS venta_id,
      vt_cliente.nombre AS cliente_nombre,
      vt_venta.total AS total,
      vt_venta.iva AS iva,
      vt_venta.descuento AS descuento")
    ->join("vt_cliente", "vt_venta.cliente_id", "=", "vt_cliente.cliente_id")
    ->where("vt_venta.venta_id", "=", $ventaId)
    ->get();


  $detalle=\App\Models\Detalle::
    selectRaw(
      "vt_detalle.detalle_id AS detalle_id,
      vt_detalle.venta_id AS venta_id,
      vt_detalle.cantidad AS cantidad,
      vt_producto.nombre as producto_nombre,
      vt_detalle.subtotal AS subtotal"
      )
    ->join("vt_producto", "vt_producto.producto_id", "=", "vt_detalle.producto_id")
    ->where("vt_detalle.venta_id", "=", $venta[0]->venta_id)
    ->get();

  $producto=\App\Models\Producto::all();

  return view('ventaDetalle')->with(["venta"=>$venta[0], "detalle"=>$detalle, "producto"=>$producto]);

});
