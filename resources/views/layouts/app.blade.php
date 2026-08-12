<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقسيط - نظام الكاشير والتقسيط (Laravel)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script>
        (function() {
            const savedTheme = localStorage.getItem('taqseet_theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <style>
        .notif-bell-btn {
            position: relative;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text-primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.4);
        }
        .notif-panel {
            display: none;
            position: absolute;
            top: 56px;
            left: 16px;
            right: 16px;
            max-width: 440px;
            margin: 0 auto;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            z-index: 3000;
            padding: 16px;
            max-height: 480px;
            overflow-y: auto;
        }
        .notif-panel.active { display: block; }
        .notif-item {
            padding: 12px;
            border-bottom: 1px solid var(--border);
            border-radius: var(--radius-sm);
            margin-bottom: 6px;
            transition: background 0.2s ease;
        }
        .notif-item.unread {
            background: rgba(99, 102, 241, 0.08);
            border-right: 4px solid var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Top App Bar with Theme Toggle and Notifications Bell -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; margin-bottom: 16px; position: relative;">
            <div style="font-weight: bold; color: var(--primary); font-size: 1.1rem; display: flex; align-items: center; gap: 6px;">
                ⚡ تقسيط <span style="font-size: 0.75rem; background: var(--border); padding: 2px 8px; border-radius: 10px; color: var(--text-secondary);">Laravel 11</span>
            </div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <button type="button" onclick="toggleNotifPanel()" class="notif-bell-btn" title="التنبيهات وإشعارات الأقساط">
                    🔔
                    <span id="notifBadge" class="notif-badge" style="display: none;">0</span>
                </button>

                <button type="button" id="themeToggleBtn" onclick="toggleTheme()" class="theme-toggle-btn">
                    <span id="themeIcon">🌙</span>
                    <span id="themeText">الوضع الداكن</span>
                </button>
            </div>

            <!-- Notifications Dropdown Panel -->
            <div id="notifPanel" class="notif-panel">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 10px; margin-bottom: 12px;">
                    <h3 style="margin: 0; font-size: 1rem; color: var(--text-primary);">🔔 إشعارات وتنبيهات الأقساط</h3>
                    <button type="button" onclick="markAllNotificationsRead()" style="background: none; border: none; color: var(--primary); font-size: 0.8rem; font-weight: bold; cursor: pointer;">
                        تحديد الكل كمقروء
                    </button>
                </div>

                <div style="margin-bottom: 10px; text-align: center;">
                    <button type="button" onclick="requestBrowserPushPermission()" class="btn btn-secondary" style="padding: 6px 12px; font-size: 0.8rem; width: 100%;">
                        📱 تفعيل إشعارات المتصفح والكمبيوتر الفورية
                    </button>
                </div>

                <div id="notifList">
                    <div style="text-align: center; color: var(--text-secondary); padding: 20px; font-size: 0.875rem;">
                        جاري تحميل الإشعارات...
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="card" style="background: #DEF7EC; color: #03543F; border-color: #84E1BC; padding: 12px 16px; margin-bottom: 16px; border-radius: var(--radius-md);">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="card" style="background: #FDE8E8; color: #9B1C1C; border-color: #F8B4B4; padding: 12px 16px; margin-bottom: 16px; border-radius: var(--radius-md);">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            الرئيسية
        </a>

        <a href="{{ route('pos.index') }}" class="nav-item {{ request()->routeIs('pos.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            الكاشير
        </a>

        <a href="{{ route('admin.postponements.index') }}" class="nav-item {{ request()->routeIs('admin.postponements.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            التأجيلات
        </a>

        <a href="{{ route('products.index') }}" class="nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            المنتجات
        </a>

        <a href="{{ route('customers.index') }}" class="nav-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            العملاء
        </a>
    </nav>

    <script>
        function updateToggleUI(theme) {
            const icon = document.getElementById('themeIcon');
            const text = document.getElementById('themeText');
            if (!icon || !text) return;
            if (theme === 'dark') {
                icon.innerText = '☀️';
                text.innerText = 'الوضع المضيء';
            } else {
                icon.innerText = '🌙';
                text.innerText = 'الوضع الداكن';
            }
        }

        function toggleTheme() {
            const current = document.documentElement.getAttribute('data-theme');
            const next = current === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('taqseet_theme', next);
            updateToggleUI(next);
        }

        function toggleNotifPanel() {
            const panel = document.getElementById('notifPanel');
            panel.classList.toggle('active');
            if (panel.classList.contains('active')) {
                loadNotifications();
            }
        }

        function loadNotifications() {
            fetch('/admin/api/notifications')
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById('notifBadge');
                    if (data.unread_count > 0) {
                        badge.innerText = data.unread_count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }

                    const listContainer = document.getElementById('notifList');
                    if (!data.notifications || data.notifications.length === 0) {
                        listContainer.innerHTML = '<div style="text-align: center; color: var(--text-secondary); padding: 20px;">لا توجد إشعارات حالياً.</div>';
                        return;
                    }

                    let html = '';
                    data.notifications.forEach(n => {
                        const unreadClass = n.is_read ? '' : 'unread';
                        html += `
                            <div class="notif-item ${unreadClass}">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px;">
                                    <strong style="font-size: 0.9rem; color: var(--text-primary);">${n.title}</strong>
                                    <span style="font-size: 0.75rem; background: var(--primary); color: white; padding: 2px 8px; border-radius: 10px;">${n.label}</span>
                                </div>
                                <div style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 6px;">${n.message}</div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <a href="${n.url}" onclick="markNotifRead('${n.id}')" style="color: var(--primary); font-size: 0.8rem; font-weight: bold; text-decoration: underline;">
                                        عرض الملف واستعراض القسط &rarr;
                                    </a>
                                </div>
                            </div>
                        `;
                    });
                    listContainer.innerHTML = html;
                })
                .catch(err => console.error(err));
        }

        function markNotifRead(id) {
            fetch(`/admin/api/notifications/${id}/read`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
        }

        function markAllNotificationsRead() {
            fetch('/admin/api/notifications/read-all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => loadNotifications());
        }

        function requestBrowserPushPermission() {
            if (!("Notification" in window)) {
                alert("المتصفح لديك لا يدعم إشعارات الشاشة الفورية.");
                return;
            }
            Notification.requestPermission().then(permission => {
                if (permission === "granted") {
                    new Notification("تقسيط - تم تفعيل الإشعارات 🔔", {
                        body: "سيتم إرسال إشعارات الأقساط المستحقة مباشرة على شاشتك عند مواعيد الاستحقاق!",
                        icon: "/favicon.ico"
                    });
                } else {
                    alert("تم رفض إذن الإشعارات من المتصفح.");
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const activeTheme = document.documentElement.getAttribute('data-theme') || 'light';
            updateToggleUI(activeTheme);
            loadNotifications();
        });
    </script>
</body>
</html>
