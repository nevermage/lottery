@extends('layout')

@section('content')
    <main>
        <div class="lotPageContainer">
            <h1 class="lotPageTitle">{{ $id }} A very long interesting title of lot</h1>
            <div class="lotPageGrid">
                <div class="lotPageImage">
                    <img src="" alt="">
                </div>
                <div class="lotPageInfo">
                    <p class="lotPageDescription">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla quam velit, vulputate eu pharetra nec, mattis ac neque. Duis vulputate commodo lectus, ac blandit elit tincidunt id. Sed rhoncus, tortor sed eleifend tristique, tortor mauris molestie elit, et lacinia ipsum quam nec dui. Quisque nec mauris sit amet elit iaculis pretium sit amet quis magna. Aenean velit odio, elementum in tempus ut, vehicula eu diam. Pellentesque rhoncus aliquam mattis. Ut vulputate eros sed felis sodales nec vulputate justo hendrerit.
                    </p>
                    <p class="lotPageAuthorName">
                        Created by: Mao Zedong
                    </p>
                    <div class="lotPageRollTimerContainer">
                        <strong class="lotPageRoll">Roll timer:</strong>
                        <strong class="lotPageTimer">2D 17:43:23</strong>
                    </div>
                    <button class="lotPageJoinButton">Take part</button>
                </div>
            </div>
        </div>
    </main>
@stop
