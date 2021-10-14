<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxController extends Controller
{
    public function producto(Request $req) {

      $data=json_decode($req->data);

      $producto=DB::table('vt_producto AS p')
        ->where('p.producto_id', '=', $data->productoId)
        ->get();

      return count($producto)>0?json_encode($producto[0]):null;

    }

    public function productoItem(Request $req) {

      $data=json_decode($req->data);
      $dataOut=[];

      $items=[];

      session_start();
      if (isset($_SESSION["items"])) {
        $items=$_SESSION["items"];
      }

      $producto=DB::table("vt_producto AS p")
        ->where("p.producto_id", "=", $data->productoId)
        ->select(
          "p.producto_id AS producto_id",
          "p.nombre AS nombre",
          "p.precio AS precio",
          "p.cantidad AS cantidad"
        )
        ->get();

      $html='';

      if (count($producto)) {

        $items[]=
          [
            "productoId"=>$producto[0]->producto_id,
            "nombre"=>$producto[0]->nombre,
            "precio"=>$producto[0]->precio,
            "cantidad"=>$data->cantidad,
          ];

      }

      $_SESSION["items"]=$items;

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
              <button type="btn btn-danger btn-sm" onclick="unsetItem($(\'#actionUnset\').val(), \''.$k.'\', \'itemsList\')">
                Eliminar
              </button>
            </td>
          </tr>';

        $total+=$i["precio"]*$i["cantidad"];

      }

      $dataOut=
        [
          "status"=>"success",
          "html"=>$html,
          "total"=>$total,
          "iva"=>ceil($total-($total/1.19)),
        ];

      return json_encode($dataOut);

    }

    public function productoItemUnset(Request $req) {

      $data=json_decode($req->data);
      $dataOut=[];

      $items=[];

      session_start();

      if (isset($_SESSION["items"])) {
        $items=$_SESSION["items"];
      }

      $html='';
      $itemsTmp=[];

      foreach($items as $k=>$i) {
        if ($data->itemId!=$k) {
          $itemsTmp[]=$i;
        }
      }

      $_SESSION["items"]=$itemsTmp;

      $total=0;

      foreach($itemsTmp as $k=>$i) {

        $html.='
          <tr>
            <td>'.($k+1).'</td>
            <td>'.$i["nombre"].'</td>
            <td>$'.number_format($i["precio"], 0, ',', '.').'</td>
            <td>'.number_format($i["cantidad"], 0, ',', '.').'</td>
            <td>$'.number_format($i["precio"]*$i["cantidad"], 0, ',', '.').'</td>
            <td>
              <button type="btn btn-danger btn-sm" onclick="unsetItem($(\'#actionUnset\').val(), \''.$k.'\', \'itemsList\')">Eliminar</button>
            </td>
          </tr>';

        $total+=$i["precio"]*$i["cantidad"];

      }

      $dataOut=
        [
          "status"=>"success",
          "html"=>$html,
          "total"=>$total,
          "iva"=>ceil($total-($total/1.19)),
        ];

      return json_encode($dataOut);

    }

    public function setVenta(Request $req) {

      session_start();
      $dataOut=[];

      $data=json_decode($req->data);

      if (!isset($_SESSION["items"])) {

        $dataOut=
          [
            "status"=>"error",
            "msg"=>"Para registrar una venta debe haber como mínimo un producto viculado a la venta."
          ];

      } else {

        $items=$_SESSION["items"];

        $venta=new \App\Models\Venta();

        $total=0;

        foreach($items as $i) {
          $total+=$i["precio"]*$i["cantidad"];
        }

        $venta->cliente_id=$data->clienteId;
        $venta->total=$total;
        $venta->fecha=date("Y-m-d H:i:s");
        $venta->descuento=($data->descuento)?$data->descuento:0;
        $venta->iva=ceil($total-($total/1.19));
        $venta->save();

        \Log::debug('VENTAS ID ' . $venta->venta_id);

        $items=$_SESSION["items"];
        $c=0;
        foreach($items as $i) {

          $detalle=new \App\Models\Detalle();
          $detalle->venta_id=$venta->venta_id;
          $detalle->cantidad=$i["cantidad"];
          $detalle->producto_id=$i["productoId"];
          $detalle->subtotal=$i["cantidad"]*$i["precio"];
          $detalle->save();
          $c++;

        }

        $_SESSION["items"]=null; 

        $dataOut=
          [
            "status"=>"success",
            "count"=>$c,
            "msg"=>"Venta registrada exitosamente"
          ];

      }

      return json_encode($dataOut);

    }

    public function setDetalleCantidad(Request $req, $detalleId = null) {

      $dataOut=[];

      $detalle=\App\Models\Detalle::where("vt_detalle.detalle_id","=", detalleId)
        ->get();
      $detalle->detalle_id=$req->detalleId;

      return json_encode($dataOut);

    }

    public function unsetVenta(Request $req, $ventaId) {

      $dataOut=
        [
          "status"=>"error",
          "msg"=>"Invocación inconsistente.",
        ];

      $venta=\App\Models\Venta::find($ventaId);
      
      if ($venta) {

        $detalle=\App\Models\Detalle::where("venta_id", "=", $ventaId)
          ->get();
        foreach($detalle as $d) {
         $d->delete();
        }

        $venta->delete();

        $dataOut=
          [
            "status"=>"success",
            "msg"=>"Venta eliminada."
          ];

      }

      return json_encode($dataOut);

    }

    public function unsetVentaProducto(Request $req, $detalleId) {

      $dataOut=
        [
          "status"=>"error",
          "msg"=>"Invocación inconsistente.",
        ];

      $detalle=\App\Models\Detalle::find($detalleId);
     
      if ($detalle) {

        $ventaId=$detalle->venta_id;
        $detalle->delete();

        $detalle=\App\Models\Detalle::where("venta_id", "=", $ventaId)->get();
        $total=0;
        foreach($detalle as $d) {
          $total+=$d->subtotal;
        }
        $venta=\App\Models\Venta::find($ventaId);
        $venta->total=$total;
        $venta->iva=ceil($total-($total/1.19));
        $venta->save();

        $dataOut=
          [
            "status"=>"success",
            "msg"=>"Producto eliminada.",
            "venta_id"=>$ventaId
          ];

      }

      return json_encode($dataOut);

    }

    public function setDetalleItem(Request $req) {

      $productoId=$req->productoId;
      $cantidad=$req->cantidad;
      $ventaId=$req->ventaId;

      $venta=\App\Models\Venta::find($ventaId);
      $producto=\App\Models\Producto::find($productoId);

      $detalle=new \App\Models\Detalle();
      $detalle->venta_id=$ventaId;
      $detalle->producto_id=$productoId;
      $detalle->cantidad=$cantidad;
      $detalle->subtotal=$cantidad*$producto->precio;
      $detalle->save();

      $detalle=\App\Models\Detalle::where("venta_id", "=", $ventaId)
        ->get();
      $total=0;
      foreach($detalle as $d) {
        $total+=$d->subtotal;
      }

      $venta->total=$total;
      $venta->iva=$total-($total/1.19);
      $venta->save();

      $dataOut=
        [
          "status"=>"success",
        ];

      return json_encode($dataOut);
    }

}
