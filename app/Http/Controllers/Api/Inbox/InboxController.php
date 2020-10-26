<?php

namespace App\Http\Controllers\Api\Inbox;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Products\CommentProductList as CommentProductResource;
use App\Models\Products\ProductComment;
use App\Models\Products\Product;
use App\User;
class InboxController extends Controller
{

    public function list(Request $request)
    {
        $by = auth()->user()->id;
        $list_inbox = ProductComment::select('product_id')
                            ->groupBy('product_id')
                            ->where(function($query) use ($by){
                                $query->where('to',$by)->orWhere('creator_id', '=',$by);
                            })
                            ->get();

        $out=[];
        foreach ($list_inbox as $row){
            $product_comment = ProductComment::orderBy('id','desc')->where('product_id',$row->product_id)->first();
            $out[] = array(
                'product_id' => $row->product_id,
                'product_name' => Product::find($row->product_id)->name,
                'last_pesan' => $product_comment->comment,
                'is_read' => $product_comment->is_read,
                'type' => $product_comment->type
            );
        }

        return response()->json([
                                'success'=>true,
                                'data'=> $out
                            ], 200);
    }

    public function list_to_admin(Request $request)
    {
        $by = auth()->user()->id;
        $list_inbox = ProductComment::select('product_id','creator_id')
                            ->groupBy('product_id','creator_id')
                            ->where('to','admin')
                            ->get();
        $out=[];
        foreach ($list_inbox as $row){
            $product_comment = ProductComment::orderBy('id','desc')->where('product_id',$row->product_id)->first();
            $out[] = array(
                'customer_id' => $row->creator_id,
                'customer_name' => User::find($row->creator_id)->name,
                'product_id' => $row->product_id,
                'product_name' => Product::find($row->product_id)->name,
                'last_pesan' => $product_comment->comment,
                'is_read' => $product_comment->is_read,
                'type' => $product_comment->type
            );
        }

        return response()->json([
                                'success'=>true,
                                'data'=> $out
                            ], 200);
    }

    public function store(Request $request)
    {
        ProductComment::create([
            'product_id'=>$request->id,
            'comment' => $request->pesan,
            'creator_id' => auth()->user()->id,
            'to' => 'admin',
            'type' => 'user',
            'is_read' => 0
        ]);

        return response()->json([
            'success' =>true
        ], 200);
    }

    public function product_comment(Request $request,$id)
    {
        $comment = $this->productObject->getProductComment($id);
        return response()->json([
            'success'=>true,
            'product_id' => $id,
            'product_comment'=>new CommentProductResource($comment)
        ], 200);
    }

    public function detail(Request $request,$id)
    {
        $comment = $this->getProductComment($id,auth()->user()->id);
        return response()->json([
            'success'=>true,
            'product_id' => $id,
            'product_comment'=>new CommentProductResource($comment)
        ], 200);
    }

    public function getProductComment($productId,$by='all')
    {
      $cooment = ProductComment::orderBy('id','asc')->where('product_id',$productId)->paginate(50);
      if($by!='all'){
          $cooment = ProductComment::orderBy('id','asc')
                                  ->where(function($query) use ($by){
                                      $query->where('to',$by)->orWhere('creator_id', '=',$by);
                                  })
                                  ->where('product_id',$productId)->get();
      }
      return $cooment;
    }

    public function detail_inbox(Request $request,$creatorId,$productId)
    {
        $comment = ProductComment::where('product_id',$productId)
                                ->where(function($query) use ($creatorId){
                                    $query->where('to',$creatorId)->orWhere('creator_id', '=',$creatorId);
                                })
                                ->get();
        return response()->json([
            'success'=>true,
            'product_comment'=>new CommentProductResource($comment)
        ], 200);
    }

    public function store_admin(Request $request,$customerId,$productId)
    {
       $chat = ProductComment::create([
            'product_id'=>$productId,
            'comment' => $request->pesan,
            'creator_id' => auth()->user()->id,
            'to' => $customerId,
            'type' => 'admin',
            'is_read' => 0
        ]);
        if($chat){
            $user = User::where('id',$customerId)->first();
            if($user->fcm_token!=null){
                $judul = "Hai ".$user->name;
                $isi = $request->pesan;
                sendMessageToDevice($judul,
                                    $isi,
                                    $user->fcm_token);
            }
        }

        return response()->json([
            'success' =>true
        ], 200);
    }
}
