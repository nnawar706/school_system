<?php

namespace App\Http\Controllers;

use App\Models\LibraryBookList;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LibraryBookListController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/library_book_list",
     *     summary="Get all library book",
     *     tags={"library book list"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all library books",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="library_shelf_id", type="integer", example=1),
     *              @OA\Property(property="library_book_category_id", type="integer", example=1),
     *              @OA\Property(property="reader_type_id", type="integer", example=1),
     *              @OA\Property(property="ISBN_no", type="string", example="123456789"),
     *              @OA\Property(property="title", type="string", example="Intro to Data Structure"),
     *              @OA\Property(property="author", type="string", example="Mr. Data"),
     *              @OA\Property(property="publisher", type="string", example=""),
     *              @OA\Property(property="cost_price", type="integer", example=1000),
     *              @OA\Property(property="quantity", type="integer", example=10),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false)
     *          )
     *      ),
     *     @OA\Response(
     *          response="204",
     *          description="No data",
     *      ),
     * )
     */


    public function index()
    {
        if(LibraryBookList::count() == 0)
        {
            return response()->json([], 204);
        }

        $list = LibraryBookList::with('library_shelf', 'library_book_category', 'reader_type')->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $list
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/library_book_list",
     *      operationId="createlibrary_book_list",
     *      tags={"library book list"},
     *      summary="Create new library book list",
     *      description="Create new library book list",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"library_shelf_id","library_book_category_id","reader_type_id","ISBN_no","title","author","publisher","cost_price","quantity"},
     *              @OA\Property(property="library_shelf_id", type="integer", example=1),
     *              @OA\Property(property="library_book_category_id", type="integer", example=1),
     *              @OA\Property(property="reader_type_id", type="integer", example=1),
     *              @OA\Property(property="ISBN_no", type="string", example="123456789"),
     *              @OA\Property(property="title", type="string", example="Intro to Data Structure"),
     *              @OA\Property(property="author", type="string", example="Mr. Data"),
     *              @OA\Property(property="publisher", type="string", example=""),
     *              @OA\Property(property="cost_price", type="integer", example=1000),
     *              @OA\Property(property="quantity", type="integer", example=10),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="Successful operation",
     *              @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=true
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="error", type="array", @OA\Items(type="string"))
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="500",
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false)
     *          )
     *      )
     * )
     */


    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'library_shelf_id' => 'required|integer',
            'library_book_category_id' => 'required|integer',
            'reader_type_id' => 'required|integer',
            'ISBN_no' => 'required|max:30|unique:library_book_list',
            'title' => 'required|max:225|min:2|string',
            'author' => 'required|max:225|min:2|string',
            'publisher' => 'nullable|string|max:255',
            'cost_price' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            LibraryBookList::create([
                'library_shelf_id' => $request->library_shelf_id,
                'library_book_category_id' => $request->library_book_category_id,
                'reader_type_id' => $request->reader_type_id,
                'ISBN_no' => $request->ISBN_no,
                'title' => $request->title,
                'author' => $request->author,
                'publisher' => $request->publisher,
                'cost_price' => $request->cost_price,
                'quantity' => $request->quantity,
                'stock_amount' => $request->quantity
            ]);

            return response()->json([
                'status' => true,
            ], 201);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false
            ], 500);
        }
    }
}
