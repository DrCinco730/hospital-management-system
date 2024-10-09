<!-- قائمة المستخدم المنسدلة -->
<div class="user-menu">
    <div class="user-profile" onclick="toggleDropdown()">
        <!-- أيقونة المستخدم -->
        <i class="fas fa-user-circle user-icon"></i>
    </div>
    <!-- القائمة المنسدلة -->
    <div id="dropdown-menu" class="dropdown-menu">
        <div class="user-info">
            <i class="fas fa-user-circle user-icon-large"></i>
            <div>
                <span class="user-name">{{ $firstName }} {{ $lastName }}</span>
                <span class="user-email">{{ $email }}</span>
            </div>
        </div>
        <div class="dropdown-options">
            <a href="#" class="dropdown-option">Account Settings</a>
            <a href="#" class="dropdown-option logout" onclick="goToLogout()">Log Out</a>
        </div>
    </div>
</div>

<style>
    /* تصميم معلومات المستخدم */

    .user-menu {
        position: fixed;
        top: 20px;
        left: 20px;
        display: flex;
        align-items: center;
        cursor: pointer;
        /* background-color: rgba(255, 255, 255, 0.3); */
        padding: 10px;
        border-radius: 10px;
        /* box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2); */
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        z-index: 1000; /* التأكد من أن الزر يظهر فوق كل العناصر الأخرى */

    }

    .user-icon {
        font-size: 40px;
        background: linear-gradient(135deg, #a6dded, #dce8f7);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-right: 10px;
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .user-icon:hover {
        transform: scale(1.2);
        color: #feb47b;
    }

    .dropdown-menu {
        position: absolute;
        top: 60px;
        left: 0;
        background: linear-gradient(135deg, #ffffff, #f0f0f0);
        border-radius: 12px;
        box-shadow: 0px 6px 16px rgba(0, 0, 0, 0.2);
        width: 310px;
        display: none;
        flex-direction: column;
        padding: 20px;
        transition: opacity 0.4s ease, transform 0.4s ease;
        opacity: 0;
        transform: translateY(-15px);
    }

    .dropdown-menu.show {
        display: flex;
        opacity: 1;
        transform: translateY(0);
    }

    .user-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #ececec;
    }

    .user-name {
        font-weight: bold;
        font-size: 18px;
        color: #333;
    }

    .user-email {
        font-size: 14px;
        color: #666;
    }

    .user-icon-large {
        font-size: 30px;
        color: #ffffff;
        margin-right: 10px;
        background-color: #444;
        border-radius: 50%;
        padding: 12px;
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    .user-icon-large:hover {
        background-color: #555;
        transform: scale(1.1);
    }

    .dropdown-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .dropdown-option {
        padding: 12px 15px;
        text-decoration: none;
        color: #333;
        font-size: 16px;
        background-color: #f8f8f8;
        border-radius: 8px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .dropdown-option:hover {
        background-color: #ececec;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
        transform: scale(1.02);
    }

    .logout {
        color: #ff4d4d;
    }

    .logout:hover {
        background-color: #ffe6e6;
    }
</style>

<script>
    function goToLogout() {
        window.location.href = "/logout"; // استبدل بالمسار الصحيح لصفحة تسجيل الخروج
    }

    function toggleDropdown() {
        var dropdown = document.getElementById('dropdown-menu');
        dropdown.classList.toggle('show');
    }

    window.onclick = function (event) {
        if (!event.target.matches('.user-profile') && !event.target.matches('.user-icon')) {
            var dropdowns = document.getElementsByClassName("dropdown-menu");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>
