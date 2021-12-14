@extends('layout')

@section('content')
<main>
    <div class="profilePageContainer">
        <div class="profilePageImageContainer">
            <img src="" alt="">
        </div>
        <div class="profilePageInfo">
            <strong class="profilePageName">{{ $id  }} Bear Grylls</strong>
            <p class="profilePageDate">In Lottery since Jan 18 2008</p>
            <div class="profilePageLotsList">
                <strong>Own lotteries</strong>
                <div class="profilePageLinks">
                    <a href="">A very long intersting title of lot</a>,
                    <a href="">A very intersting title of lot</a>,
                    <a href="">A very long intersting title of lot</a>,
                    <a href="">A title of lot</a>
                </div>
            </div>
            <div class="profilePageLotsList">
                <strong>Won lotteries</strong>
                <div class="profilePageLinks">
                    <a href="">A very long intersting title of lot</a>,
                    <a href="">A very intersting title of lot</a>,
                    <a href="">A very long intersting title of lot</a>,
                    <a href="">A title of lot</a>
                </div>
            </div>
        </div>
    </div>
</main>
@stop
