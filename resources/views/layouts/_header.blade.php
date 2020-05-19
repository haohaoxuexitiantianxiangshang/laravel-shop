<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
    <div class="container">
        <!-- Branding Image -->
        <a class="navbar-brand " href="{{ url('/') }}">
            店
        </a>
        <ul class="navbar-nav navbar-right">
            @guest
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">登录</a></li>
            @else
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }}
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a href="{{ route('business.orders.index') }}" class="dropdown-item">订单</a>
                        <a class="dropdown-item" id="logout" href="#"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">退出</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
        @endguest
        <!-- 登录注册链接结束 -->
        </ul>
    </div>
</nav>
