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
                <span class="user-name">{{ $name }}</span>
                <span class="user-email">{{ $email }}</span>

            </div>
        </div>
        <div class="dropdown-options">
            @php
            $type = \App\Services\Login\LoginService::typeOfUser()
            @endphp
            @if($type === 'user')
            <a href="/user/edit" class="dropdown-option">Account Settings</a>
            @endif
            <a href="#" class="dropdown-option logout" onclick="goToLogout()">Log Out</a>
        </div>
    </div>
</div>

<!-- روابط المكتبات CSS -->
<link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Poppins&amp;display=swap'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- روابط المكتبات JS -->
<script src="https://cdn.jsdelivr.net/npm/notiflix@3/dist/notiflix-aio-3.2.5.min.js"></script>

<style>

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
    }

    /* تحسين تلوين الأيقونة */
    .user-icon {
        font-size: 40px;
        background: linear-gradient(135deg, #a6dded, #dce8f7);
        /* تدرج لوني سماوي */
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-right: 10px;
        transition: transform 0.3s ease, color 0.3s ease;
    }


    /* تكبير الأيقونة عند التفاعل */
    .user-icon:hover {
        transform: scale(1.2);
        color: #feb47b;
    }

    /* تحسين تصميم القائمة المنسدلة */
    .dropdown-menu {
        position: absolute;
        top: 60px;
        left: 0;
        background: linear-gradient(135deg, #ffffff, #f0f0f0);
        border-radius: 12px;
        box-shadow: 0px 6px 16px rgba(0, 0, 0, 0.2);
        width: 270px;
        display: none;
        flex-direction: column;
        padding: 20px;
        transition: opacity 0.4s ease, transform 0.4s ease;
        opacity: 0;
        transform: translateY(-15px);
    }

    /* عرض القائمة عند إضافة فئة .show */
    .dropdown-menu.show {
        display: flex;
        opacity: 1;
        transform: translateY(0);
    }

    /* تصميم المعلومات داخل القائمة */
    .user-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #ececec;
    }

    /* تحسين حجم الخطوص والألوان */
    .user-name {
        font-weight: bold;
        font-size: 16px;
        color: #333;
    }

    .user-email {
        font-size: 13px;
        color: #666;
    }

    /* الأيقونة الكبيرة داخل القائمة */
    .user-icon-large {
        font-size: 28px;
        color: #ffffff;
        margin-right: 10px;
        background-color: #444;
        border-radius: 50%;
        padding: 12px;
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    /* تغيير لون الأيقونة عند التفاعل */
    .user-icon-large:hover {
        background-color: #555;
        transform: scale(1.1);
    }

    /* تصميم الخيارات */
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

    /* تغيير لون الخلفية عند التفاعل */
    .dropdown-option:hover {
        background-color: #ececec;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
        transform: scale(1.02);
    }

    /* زر تسجيل الخروج بلون أحمر */
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
