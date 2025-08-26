<ul class="account-nav">
    <li><a href="{{ route('user.index') }}" class="menu-link menu-link_us-s">Dashboard</a></li>
    <li><a href="{{ route('user.order') }}" class="menu-link menu-link_us-s">Orders</a></li>
    <li><a href="account-address.html" class="menu-link menu-link_us-s">Addresses</a></li>
    <li><a href="account-details.html" class="menu-link menu-link_us-s">Account Details</a></li>
    <li><a href="account-wishlist.html" class="menu-link menu-link_us-s">Wishlist</a></li>
    <li>
        <form action="{{ route('logout') }}" method="POST" id="logout-form" style="display: none;">
            @csrf
        </form>
        <a href="javascript:void(0)" class="menu-link menu-link_us-s"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>
    </li>
</ul>
