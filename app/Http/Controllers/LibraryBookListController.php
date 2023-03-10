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
     *              @OA\Property(property="taken_by", type="integer", example=0),
     *              @OA\Property(property="library_shelf", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="children's corner"),
     *              ),
     *              @OA\Property(property="library_book_category", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="story book"),
     *              ),
     *              @OA\Property(property="reader_type", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="early reader"),
     *              ),
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
        $branch_id = (new AuthController)->getBranch();

        if(LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })->doesntExist())
        {
            return response()->json([], 204);
        }

        $list = LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })
        ->with(['library_shelf' => function($query) {
            return $query->select('id','name');
        }])
        ->with('library_book_category','reader_type')
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'data' => $list
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/library_book_list/by_branch/{branch_id}",
     *     summary="Get all library book under a branch",
     *     tags={"library book list"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all library books under one branch",
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
     *              @OA\Property(property="taken_by", type="integer", example=0),
     *              @OA\Property(property="library_shelf", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="children's corner"),
     *              ),
     *              @OA\Property(property="library_book_category", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="story book"),
     *              ),
     *              @OA\Property(property="reader_type", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="early reader"),
     *              ),
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


    public function readByBranch($branch_id)
    {
        if(LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })->doesntExist())
        {
            return response()->json([], 204);
        }

        $list = LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })
            ->with(['library_shelf' => function($query)
            {
                return $query->select('id','name');
            }])
            ->with('library_book_category','reader_type')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $list
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/library_book_list/by_category/{category_id}",
     *     summary="Get all library book under a category",
     *     tags={"library book list"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all library books under one category",
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
     *              @OA\Property(property="taken_by", type="integer", example=0),
     *              @OA\Property(property="library_shelf", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="children's corner"),
     *              ),
     *              @OA\Property(property="library_book_category", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="story book"),
     *              ),
     *              @OA\Property(property="reader_type", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="early reader"),
     *              ),
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


    public function readByCategory($category_id)
    {
        $branch_id = (new AuthController)->getBranch();

        if(LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })->where('library_book_category_id', $category_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $list = LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })
            ->where('library_book_category_id', $category_id)
            ->with(['library_shelf' => function($query)
            {
                return $query->select('id','name');
            }])
            ->with('library_book_category','reader_type')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $list
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/library_book_list/by_reader_type/{type_id}",
     *     summary="Get all library book under a reader type",
     *     tags={"library book list"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all library books under one reader type",
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
     *              @OA\Property(property="taken_by", type="integer", example=0),
     *              @OA\Property(property="library_shelf", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="children's corner"),
     *              ),
     *              @OA\Property(property="library_book_category", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="story book"),
     *              ),
     *              @OA\Property(property="reader_type", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="early reader"),
     *              ),
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


    public function readByReaderType($type_id)
    {
        $branch_id = (new AuthController)->getBranch();

        if(LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })->where('reader_type_id', $type_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $list = LibraryBookList::whereHas('library_shelf', function($query) use($branch_id) {
            $query->where('branch_id', $branch_id);
        })
            ->where('reader_type_id', $type_id)
            ->with(['library_shelf' => function($query)
            {
                return $query->select('id','name');
            }])
            ->with('library_book_category','reader_type')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $list
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/library_book_list/isbn_no={isbn}",
     *     summary="Get a book using ISBN no",
     *     tags={"library book list"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="library book using ISBN_no fetched",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data",
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
     *              @OA\Property(property="taken_by", type="integer", example=0),
     *              @OA\Property(property="library_shelf", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="branch_id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="children's corner"),
     *                                  @OA\Property(property="branch", type="object",
     *                                              @OA\Property(property="id", type="integer", example=1),
     *                                              @OA\Property(property="name", type="string", example="Main branch"),
     *                                  )
     *              ),
     *              @OA\Property(property="library_book_category", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="story book"),
     *              ),
     *              @OA\Property(property="reader_type", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="early reader"),
     *              ),
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


    public function readByISBN(Request $request, $isbn_no)
    {
        if(!LibraryBookList::where('ISBN_no', $isbn_no)->doesntExist())
        {
            $book = LibraryBookList::where('ISBN_no', $isbn_no)->with(['library_shelf.branch' => function($query)
            {
                return $query->select('id','name');
            }])
                ->with('library_book_category','reader_type')->get();

            return response()->json([
                'status' => true,
                'data' => $book[0]
            ], 200);
        }

        return response()->json([], 204);
    }


    /**
     * @OA\Get(
     *     path="/api/library_book_list/available_books",
     *     summary="Get all available library books",
     *     tags={"library book list"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all library books that are available",
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
     *              @OA\Property(property="taken_by", type="integer", example=0),
     *              @OA\Property(property="library_shelf", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="children's corner"),
     *              ),
     *              @OA\Property(property="library_book_category", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="story book"),
     *              ),
     *              @OA\Property(property="reader_type", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="early reader"),
     *              ),
     *                 ),
     *             ),
     *         ),
     *     ),
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


    public function readByAvailability()
    {
        $branch_id = (new AuthController)->getBranch();

        if(LibraryBookList::whereRaw('quantity - taken_by > 0')
            ->whereHas('library_shelf', function($query) use($branch_id) {
                $query->where('branch_id', $branch_id);
            })->exists())
        {
            $list = LibraryBookList::whereRaw('quantity - taken_by > 0')
                ->whereHas('library_shelf', function($query) use($branch_id) {
                    $query->where('branch_id', $branch_id);
                })
                ->with(['library_shelf' => function($query)
                {
                    return $query->select('id','name');
                }])
                ->with('library_book_category', 'reader_type')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $list
            ], 200);
        }
        return response()->json([], 204);
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
            'publisher' => 'nullable|string|max:255|min:5',
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


    /**
     * @OA\Put(
     *      path="/api/library_book_list/{id}",
     *      summary="Update a library book list",
     *      description="Update a library book list by id",
     *      operationId="updatelibrary_book_list",
     *      tags={"library book list"},
     *      @OA\Parameter(
     *          name="id",
     *          description="library_book_list ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="library book list object that needs to be updated",
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
     *          response=200,
     *          description="library book list updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=true
     *              ),
     *              )
     *          )
     *      ),
     * )
     */

    public function update(Request $request, $id)
    {
        $list = LibraryBookList::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'library_shelf_id' => 'required|integer',
            'library_book_category_id' => 'required|integer',
            'reader_type_id' => 'required|integer',
            'ISBN_no' => 'required|max:30|unique:library_book_list,ISBN_no'.$id,
            'title' => 'required|max:225|min:2|string',
            'author' => 'required|max:225|min:2|string',
            'publisher' => 'nullable|string|max:255|min:5',
            'cost_price' => 'required|integer',
            'quantity' => 'required|integer',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $list->update([
                'library_shelf_id' => $request->library_shelf_id,
                'library_book_category_id' => $request->library_book_category_id,
                'reader_type_id' => $request->reader_type_id,
                'ISBN_no' => $request->ISBN_no,
                'title' => $request->title,
                'author' => $request->author,
                'publisher' => $request->publisher,
                'cost_price' => $request->cost_price,
                'quantity' => $request->quantity,
            ]);

            return response()->json([
                'status' => true,
            ], 200);
        }
        catch(QueryException $ex)
        {
            return response()->json([
                'status' => false], 304);
        }
    }


    /**
     * @OA\Delete(
     *      path="/api/library_book_list/{id}",
     *      operationId="deletelibrary_book_list",
     *      tags={"library book list"},
     *      summary="Delete a library book",
     *      description="Delete a library book by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the library book",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *          ),
     *      ),
     * )
     */


    public function delete($id)
    {
        $list = LibraryBookList::findOrFail($id);
        try
        {
            $list->delete();

            return response()->json([
                'status' => true], 200);
        }
        catch(QueryException $ex)
        {
            return response()->json([
                'status' => false
            ], 304);
        }
    }
}
