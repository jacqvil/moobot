<?php

namespace MooBot\Http\Controllers;

use Illuminate\Http\Request;
use OneApi\OneApiClient;
use OneApi\OneApiClientInterface;

class MessengerController extends Controller
{
    /**
     * @var OneApiClientInterface
     */
    protected $oneApiClient;

    /**
     * MessengerController constructor.
     *
     * @param OneApiClientInterface $oneApiClient
     */
    public function __construct(OneApiClientInterface $oneApiClient)
    {
        $this->oneApiClient = $oneApiClient;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->oneApiClient->authenticate();
        $customers = $this->oneApiClient->customers('27833884078');
        $this->oneApiClient->recipients($customers[0]->id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
