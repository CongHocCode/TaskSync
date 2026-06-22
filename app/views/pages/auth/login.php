<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskSync - Đăng nhập</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            background-color: #0b0c10;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .bg-grid {
            background-image: linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),
                              linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="text-gray-200 min-h-screen flex items-stretch">

    <div class="hidden lg:flex w-1/2 bg-slate-950 p-12 flex-col justify-between relative overflow-hidden bg-grid border-r border-slate-800">
        <div class="flex items-center gap-3 z-10">
            <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center font-bold text-xl shadow-lg shadow-purple-500/30 text-white">TS</div>
            <span class="text-xl font-bold tracking-wider text-white">TaskSync</span>
        </div>
        
        <div class="max-w-xl z-10 my-auto">
            <span class="text-xs font-semibold tracking-widest text-purple-400 uppercase">Next-Gen Project Management</span>
            <h1 class="text-5xl font-extrabold text-white leading-tight mt-3 mb-6">Sắp xếp hoàn hảo.<br>Công việc trôi chảy.</h1>
            <p class="text-slate-400 text-lg leading-relaxed mb-8">Hệ thống quản lý công việc và dự án tinh gọn dành cho các đội nhóm.</p>
            <div class="flex gap-4">
                <span class="px-4 py-2 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-300">⚡ Epic Tracking</span>
                <span class="px-4 py-2 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-300">📊 Agile Sprints</span>
                <span class="px-4 py-2 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-300">🎛️ Admin Control</span>
            </div>
        </div>
        
        <div class="text-xs text-slate-500 z-10">TaskSync v1.0 | 2026</div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-slate-900">
        <div class="w-full max-w-md space-y-8 bg-slate-950 p-8 rounded-2xl border border-slate-800 shadow-2xl">
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-bold text-white tracking-tight">Chào mừng trở lại</h2>
                <p class="mt-2 text-sm text-slate-400">Đăng nhập vào không gian làm việc của bạn</p>
            </div>

            <form class="mt-8 space-y-6" action="http://127.0.0.1:8000/dashboard" method="GET">
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email hoặc Username</label>
                        <input id="username" name="username" type="text" required class="mt-1 block w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all" placeholder="admin" value="admin">
                    </div>
                    <div>
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold uppercase tracking-wider text-slate-400">Mật khẩu</label>
                            <a href="#" class="text-xs text-purple-400 hover:underline">Quên mật khẩu?</a>
                        </div>
                        <input id="password" name="password" type="password" required class="mt-1 block w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-600 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all" placeholder="••••••" value="123456">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 px-4 bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white font-semibold rounded-xl shadow-lg shadow-purple-600/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-2 cursor-pointer">
                    Đăng nhập <span>➔</span>
                </button>

                <div class="text-center text-sm text-slate-400">
                    Chưa có tài khoản? <a href="#" class="text-purple-400 hover:underline font-medium">Đăng ký ngay</a>
                </div>

                <div class="pt-6 border-t border-slate-800/60">
                    <p class="text-center text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Thử nghiệm nhanh hệ thống (Quick Accounts)</p>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="setAccount('admin')" class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 text-center transition-all cursor-pointer group">
                            <span class="block text-slate-400 group-hover:text-purple-400 text-sm font-semibold">🛡️ Admin</span>
                            <span class="text-[10px] text-slate-500">Át Min</span>
                        </button>
                        <button type="button" onclick="setAccount('pm')" class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 text-center transition-all cursor-pointer group">
                            <span class="block text-slate-400 group-hover:text-purple-400 text-sm font-semibold">👤 PM</span>
                            <span class="text-[10px] text-slate-500">Alex Rivera</span>
                        </button>
                        <button type="button" onclick="setAccount('member')" class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 text-center transition-all cursor-pointer group">
                            <span class="block text-slate-400 group-hover:text-purple-400 text-sm font-semibold">👤 Member</span>
                            <span class="text-[10px] text-slate-500">Sarah Chen</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setAccount(role) {
            const user = document.getElementById('username');
            const pass = document.getElementById('password');
            if(role === 'admin') { user.value = 'admin'; pass.value = '123456'; }
            if(role === 'pm') { user.value = 'pm_alex'; pass.value = '123456'; }
            if(role === 'member') { user.value = 'member_sarah'; pass.value = '123456'; }
        }
    </script>
</body>
</html>
