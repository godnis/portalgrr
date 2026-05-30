<!DOCTYPE html>
<html lang="pt-br" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'GRR • PRF — Acesso')</title>
    <meta name="description" content="Portal Operacional da GRR — Ambiente restrito de autenticação institucional.">
    <meta name="theme-color" content="#08111f">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root{
            --gov-bg-1:#02060f;
            --gov-bg-2:#06101d;
            --gov-bg-3:#0a1730;
            --gov-bg-4:#0d203e;

            --gov-card:#101b2c;
            --gov-card-2:#162235;
            --gov-card-soft:rgba(255,255,255,.035);

            --gov-border:rgba(255,255,255,.08);
            --gov-border-strong:rgba(255,255,255,.12);

            --gov-text:#eef4ff;
            --gov-muted:#aebbd0;
            --gov-soft:#8f9db2;

            --gov-gold:#d4af37;
            --gov-gold-2:#f0c85a;
            --gov-green:#2fd17b;
            --gov-red:#ff6b7a;
            --gov-red-soft:#ffccd3;
            --gov-info:#57c7ff;
            --gov-info-soft:#d9f3ff;

            --gov-shadow:0 22px 55px rgba(0,0,0,.32);
            --gov-radius-xl:24px;
            --gov-radius-lg:18px;
            --gov-radius-md:14px;
        }

        html, body{
            height:100%;
            min-height:100%;
        }

        body.gov-body{
            margin:0;
            color:var(--gov-text);
            font-family:"Segoe UI", Inter, system-ui, -apple-system, BlinkMacSystemFont, "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(circle at 12% 18%, rgba(212,175,55,.11), transparent 24%),
                radial-gradient(circle at 84% 22%, rgba(24,107,255,.16), transparent 26%),
                radial-gradient(circle at 72% 76%, rgba(87,199,255,.10), transparent 24%),
                linear-gradient(135deg, var(--gov-bg-1) 0%, var(--gov-bg-2) 30%, var(--gov-bg-3) 70%, var(--gov-bg-4) 100%);
            position:relative;
        }

        body.gov-body::before{
            content:"";
            position:fixed;
            inset:0;
            pointer-events:none;
            opacity:.18;
            background-image:
                linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
            background-size:38px 38px;
            mask-image:radial-gradient(circle at center, rgba(0,0,0,.95), rgba(0,0,0,.20));
            animation:govGridPulse 7s ease-in-out infinite alternate;
            z-index:0;
        }

        body.gov-body::after{
            content:"";
            position:fixed;
            inset:-20%;
            pointer-events:none;
            opacity:.42;
            background:
                radial-gradient(circle, rgba(255,255,255,.10) 0 1px, transparent 1.2px) 0 0/110px 110px,
                radial-gradient(circle, rgba(87,199,255,.12) 0 1px, transparent 1.2px) 20px 40px/160px 160px,
                linear-gradient(180deg, transparent 0%, rgba(255,255,255,.03) 48%, transparent 50%, transparent 100%);
            animation:
                govParticlesMove 28s linear infinite,
                govScanline 8s linear infinite;
            z-index:0;
        }

        @keyframes govGridPulse{
            from{ opacity:.10; transform:scale(1); }
            to{ opacity:.18; transform:scale(1.02); }
        }

        @keyframes govParticlesMove{
            from{ transform:translate3d(0,0,0); }
            to{ transform:translate3d(-80px,-120px,0); }
        }

        @keyframes govScanline{
            0%   { background-position:0 0, 20px 40px, 0 -100vh; }
            100% { background-position:0 -80px, 20px -120px, 0 100vh; }
        }

        .gov-cyber-bg{
            position:fixed;
            inset:0;
            pointer-events:none !important;
            z-index:0;
            overflow:hidden;
        }

        .gov-cyber-bg__ring,
        .gov-cyber-bg__ring::before,
        .gov-cyber-bg__ring::after{
            position:absolute;
            border-radius:50%;
            content:"";
            pointer-events:none !important;
        }

        .gov-cyber-bg__ring{
            width:580px;
            height:580px;
            right:-120px;
            top:12%;
            border:1px solid rgba(87,199,255,.12);
            box-shadow:
                0 0 0 24px rgba(87,199,255,.025),
                0 0 0 54px rgba(87,199,255,.018);
            animation:govHudRotate 26s linear infinite;
        }

        .gov-cyber-bg__ring::before{
            inset:58px;
            border:1px dashed rgba(240,200,90,.18);
            animation:govHudRotateReverse 22s linear infinite;
        }

        .gov-cyber-bg__ring::after{
            inset:122px;
            border:1px solid rgba(255,255,255,.06);
        }

        .gov-cyber-bg__ring--left{
            width:420px;
            height:420px;
            left:-110px;
            bottom:-70px;
            top:auto;
            right:auto;
            border-color:rgba(212,175,55,.12);
            box-shadow:
                0 0 0 18px rgba(212,175,55,.03),
                0 0 0 42px rgba(212,175,55,.018);
            animation-duration:30s;
        }

        .gov-cyber-bg__ring--left::before{
            border-color:rgba(87,199,255,.14);
        }

        .gov-cyber-bg__hudline{
            position:absolute;
            inset:auto 0 16%;
            height:1px;
            background:linear-gradient(90deg, transparent 0%, rgba(87,199,255,.35) 20%, rgba(255,255,255,.12) 50%, rgba(240,200,90,.35) 80%, transparent 100%);
            box-shadow:0 0 18px rgba(87,199,255,.12);
            animation:govLineFloat 9s ease-in-out infinite;
            pointer-events:none !important;
        }

        .gov-cyber-bg__hudline--top{
            inset:18% 0 auto;
            animation-delay:-4s;
        }

        .gov-cyber-bg__dots{
            position:absolute;
            inset:0;
            background:
                radial-gradient(circle at 10% 20%, rgba(47,209,123,.7) 0 3px, transparent 4px),
                radial-gradient(circle at 22% 74%, rgba(87,199,255,.5) 0 2px, transparent 3px),
                radial-gradient(circle at 78% 18%, rgba(240,200,90,.7) 0 3px, transparent 4px),
                radial-gradient(circle at 82% 64%, rgba(87,199,255,.55) 0 2px, transparent 3px),
                radial-gradient(circle at 62% 36%, rgba(255,255,255,.3) 0 2px, transparent 3px);
            opacity:.9;
            animation:govDotsBlink 5s ease-in-out infinite alternate;
            pointer-events:none !important;
        }

        @keyframes govHudRotate{
            from{ transform:rotate(0deg); }
            to{ transform:rotate(360deg); }
        }

        @keyframes govHudRotateReverse{
            from{ transform:rotate(360deg); }
            to{ transform:rotate(0deg); }
        }

        @keyframes govLineFloat{
            0%,100%{ transform:translateY(0); opacity:.55; }
            50%{ transform:translateY(-10px); opacity:.9; }
        }

        @keyframes govDotsBlink{
            from{ opacity:.28; }
            to{ opacity:.92; }
        }

        a{
            text-decoration:none;
        }

        .gov-restricted-bar{
            position:relative;
            top:0;
            z-index:20;
            backdrop-filter:blur(12px);
            background:rgba(2, 7, 14, .72);
            border-bottom:1px solid rgba(255,255,255,.05);
            box-shadow:0 8px 20px rgba(0,0,0,.18);
        }

        .gov-restricted-inner{
            min-height:38px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            padding:0 14px;
            font-size:.72rem;
            color:var(--gov-muted);
            letter-spacing:.14em;
            text-transform:uppercase;
        }

        .gov-restricted-left,
        .gov-restricted-right{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        }

        .gov-dot{
            width:9px;
            height:9px;
            border-radius:999px;
            background:linear-gradient(135deg, var(--gov-green), #0fa95f);
            box-shadow:0 0 0 4px rgba(47,209,123,.12), 0 0 18px rgba(47,209,123,.35);
            display:inline-block;
        }

        .gov-sep{
            color:rgba(255,255,255,.18);
        }

        .gov-topbar{
            position:relative;
            z-index:19;
            background:linear-gradient(180deg, rgba(255,255,255,.025), rgba(255,255,255,.01));
            border-bottom:1px solid rgba(255,255,255,.05);
            backdrop-filter:blur(10px);
        }

        .gov-topbar-inner{
            min-height:74px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:20px;
            padding:0 14px;
        }

        .gov-emblem{
            width:46px;
            height:46px;
            border-radius:15px;
            display:grid;
            place-items:center;
            position:relative;
            background:
                linear-gradient(145deg, rgba(212,175,55,.16), rgba(212,175,55,.05)),
                rgba(255,255,255,.03);
            border:1px solid rgba(212,175,55,.26);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.08),
                0 10px 20px rgba(0,0,0,.22),
                0 0 24px rgba(240,200,90,.06);
        }

        .gov-emblem::before{
            content:"";
            width:19px;
            height:19px;
            border-radius:7px;
            border:2px solid rgba(212,175,55,.85);
            transform:rotate(45deg);
            position:absolute;
        }

        .gov-emblem-dot{
            width:7px;
            height:7px;
            border-radius:999px;
            background:var(--gov-gold-2);
            position:relative;
            z-index:1;
            box-shadow:0 0 14px rgba(240,200,90,.55);
        }

        .gov-org-title{
            font-size:1rem;
            font-weight:800;
            letter-spacing:.05em;
            color:#fff;
            text-transform:uppercase;
        }

        .gov-org-subtitle{
            font-size:.81rem;
            color:var(--gov-muted);
            margin-top:2px;
        }

        .gov-meta{
            font-size:.83rem;
            color:var(--gov-text);
        }

        .gov-meta .text-muted{
            color:var(--gov-muted)!important;
        }

        .gov-topbar-strip{
            height:2px;
            background:linear-gradient(90deg,
                rgba(212,175,55,0) 0%,
                rgba(212,175,55,.85) 20%,
                rgba(255,255,255,.36) 50%,
                rgba(212,175,55,.85) 80%,
                rgba(212,175,55,0) 100%);
            opacity:.88;
        }

        .gov-auth-page{
            position:relative;
            z-index:5;
            min-height:calc(100vh - 114px);
            display:flex;
            align-items:center;
            justify-content:center;
            padding:18px 14px;
            background:transparent !important;
            overflow:hidden;
        }

        .gov-auth-wrap{
            width:100%;
            max-width:980px;
            margin:0 auto;
            background:transparent !important;
            position:relative;
            z-index:6;
        }

        .gov-auth-card{
            position:relative;
            z-index:7;
            overflow:hidden;
            border-radius:var(--gov-radius-xl);
            border:1px solid var(--gov-border);
            background:linear-gradient(135deg, rgba(19,30,46,.96), rgba(15,24,38,.96));
            box-shadow:
                var(--gov-shadow),
                0 0 0 1px rgba(255,255,255,.025),
                0 0 80px rgba(19,81,180,.12);
            backdrop-filter:blur(14px);
            isolation:isolate;
        }

        .gov-auth-card::before{
            content:"";
            position:absolute;
            inset:0;
            pointer-events:none;
            background:
                radial-gradient(circle at top right, rgba(212,175,55,.07), transparent 24%),
                radial-gradient(circle at bottom left, rgba(65,105,225,.08), transparent 30%);
        }

        .gov-auth-card::after{
            content:"";
            position:absolute;
            inset:-24px;
            z-index:-1;
            background:
                radial-gradient(circle at 30% 50%, rgba(240,200,90,.16), transparent 38%),
                radial-gradient(circle at 72% 42%, rgba(87,199,255,.12), transparent 34%);
            filter:blur(60px);
            opacity:.75;
            pointer-events:none;
        }

        .gov-auth-grid{
            display:grid;
            grid-template-columns:.92fr 1.08fr;
            min-height:0;
        }

        .gov-auth-side{
            position:relative;
            padding:22px 22px 18px;
            border-right:1px solid rgba(255,255,255,.06);
            background:
                linear-gradient(180deg, rgba(212,175,55,.03), rgba(255,255,255,.01)),
                rgba(255,255,255,.015);
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            gap:14px;
        }

        .gov-auth-side::after{
            content:"";
            position:absolute;
            inset:0;
            pointer-events:none;
            background:
                linear-gradient(135deg, transparent 0%, rgba(255,255,255,.02) 50%, transparent 100%),
                repeating-linear-gradient(180deg, rgba(255,255,255,.02) 0 1px, transparent 1px 24px);
            opacity:.45;
        }

        .gov-auth-side__top,
        .gov-auth-side__center,
        .gov-auth-side__bottom{
            position:relative;
            z-index:1;
        }

        .gov-auth-brand{
            display:flex;
            align-items:flex-start;
            gap:12px;
        }

        .gov-auth-emblem{
            width:52px;
            height:52px;
            border-radius:16px;
            flex:0 0 52px;
            position:relative;
            display:grid;
            place-items:center;
            background:
                linear-gradient(145deg, rgba(212,175,55,.15), rgba(212,175,55,.04)),
                rgba(255,255,255,.028);
            border:1px solid rgba(212,175,55,.24);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.08),
                0 10px 22px rgba(0,0,0,.20);
        }

        .gov-auth-emblem::before{
            content:"";
            width:22px;
            height:22px;
            border-radius:7px;
            border:2px solid rgba(212,175,55,.82);
            transform:rotate(45deg);
            position:absolute;
        }

        .gov-auth-emblem-dot{
            width:8px;
            height:8px;
            border-radius:999px;
            background:var(--gov-gold-2);
            position:relative;
            z-index:1;
            box-shadow:0 0 14px rgba(240,200,90,.55);
        }

        .gov-auth-brand__eyebrow{
            font-size:.67rem;
            color:var(--gov-gold-2);
            letter-spacing:.14em;
            text-transform:uppercase;
            margin-bottom:4px;
            font-weight:800;
        }

        .gov-auth-brand__title{
            font-size:1.55rem;
            line-height:1;
            font-weight:900;
            letter-spacing:.04em;
            color:#fff;
            text-transform:uppercase;
            margin-bottom:6px;
        }

        .gov-auth-brand__sub{
            font-size:.90rem;
            color:var(--gov-muted);
            line-height:1.45;
        }

        .gov-auth-side__meta{
            margin-top:12px;
        }

        .gov-auth-chip{
            display:inline-flex;
            align-items:center;
            gap:10px;
            min-height:34px;
            padding:0 13px;
            border-radius:999px;
            background:rgba(255,255,255,.045);
            border:1px solid rgba(255,255,255,.08);
            color:#eef4ff;
            font-size:.74rem;
            letter-spacing:.08em;
            text-transform:uppercase;
            font-weight:700;
            box-shadow:0 0 0 1px rgba(255,255,255,.015);
        }

        .gov-auth-chip__dot{
            width:8px;
            height:8px;
            border-radius:999px;
            background:linear-gradient(135deg, var(--gov-green), #14b96b);
            box-shadow:0 0 0 4px rgba(47,209,123,.12), 0 0 18px rgba(47,209,123,.28);
        }

        .gov-auth-side__hint{
            margin-top:12px;
            max-width:420px;
            color:var(--gov-text);
            font-size:.93rem;
            line-height:1.58;
            font-weight:600;
        }

        .gov-auth-panel{
            max-width:420px;
            padding:15px 16px;
            border-radius:17px;
            background:rgba(255,255,255,.035);
            border:1px solid rgba(255,255,255,.07);
            box-shadow:inset 0 1px 0 rgba(255,255,255,.03), 0 10px 24px rgba(0,0,0,.08);
        }

        .gov-auth-panel__title{
            font-size:.74rem;
            font-weight:800;
            letter-spacing:.14em;
            text-transform:uppercase;
            color:var(--gov-gold-2);
            margin-bottom:10px;
        }

        .gov-auth-panel__list{
            list-style:none;
            margin:0;
            padding:0;
            display:grid;
            gap:7px;
        }

        .gov-auth-panel__list li{
            position:relative;
            padding-left:16px;
            color:var(--gov-text);
            line-height:1.44;
            font-size:.92rem;
        }

        .gov-auth-panel__list li::before{
            content:"";
            width:6px;
            height:6px;
            border-radius:999px;
            background:var(--gov-gold);
            position:absolute;
            left:0;
            top:.64em;
            transform:translateY(-50%);
            box-shadow:0 0 0 4px rgba(212,175,55,.08);
        }

        .gov-auth-side__line{
            width:100%;
            height:1px;
            background:linear-gradient(90deg,
                rgba(255,255,255,0) 0%,
                rgba(255,255,255,.10) 15%,
                rgba(255,255,255,.10) 85%,
                rgba(255,255,255,0) 100%);
            margin-bottom:10px;
        }

        .gov-auth-side__foot{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            flex-wrap:wrap;
        }

        .gov-auth-lock{
            display:inline-flex;
            align-items:center;
            gap:10px;
            color:#edf3ff;
            font-size:.75rem;
            letter-spacing:.08em;
            text-transform:uppercase;
            font-weight:700;
        }

        .gov-auth-dot{
            width:8px;
            height:8px;
            border-radius:999px;
            background:linear-gradient(135deg, var(--gov-green), #18b36a);
            box-shadow:0 0 0 4px rgba(47,209,123,.12);
        }

        .gov-auth-side__mini{
            color:var(--gov-soft);
            font-size:.79rem;
            font-weight:600;
        }

        .gov-auth-main{
            position:relative;
            padding:22px 22px 18px;
            display:flex;
            flex-direction:column;
            justify-content:center;
            background:
                linear-gradient(180deg, rgba(255,255,255,.014), rgba(255,255,255,.006)),
                linear-gradient(135deg, rgba(87,199,255,.02), transparent 36%);
        }

        .gov-auth-main::before{
            content:"";
            position:absolute;
            inset:0;
            pointer-events:none;
            background:
                linear-gradient(90deg, transparent 0%, rgba(255,255,255,.018) 48%, transparent 50%),
                radial-gradient(circle at 88% 8%, rgba(240,200,90,.08), transparent 16%);
            opacity:.7;
        }

        .gov-auth-head{
            margin-bottom:12px;
            position:relative;
            z-index:1;
        }

        .gov-auth-back{
            display:inline-flex;
            align-items:center;
            gap:8px;
            color:#c7d3e6;
            font-size:.88rem;
            margin-bottom:12px;
            transition:.18s ease;
            font-weight:700;
        }

        .gov-auth-back:hover{
            color:#fff;
        }

        .gov-auth-kicker{
            display:inline-block;
            margin-bottom:10px;
            padding:5px 11px;
            border-radius:999px;
            background:rgba(212,175,55,.10);
            border:1px solid rgba(212,175,55,.18);
            color:var(--gov-gold-2);
            font-size:.69rem;
            font-weight:800;
            letter-spacing:.12em;
            text-transform:uppercase;
        }

        .gov-auth-title{
            margin:0;
            font-size:clamp(1.8rem, 2vw, 2.55rem);
            line-height:.98;
            font-weight:900;
            color:#fff;
            letter-spacing:-.03em;
            text-shadow:0 4px 18px rgba(0,0,0,.24);
        }

        .gov-auth-subtitle{
            margin:8px 0 0;
            color:#c8d4e8;
            font-size:.95rem;
            line-height:1.54;
            max-width:430px;
            font-weight:600;
        }

        .gov-auth-body{
            position:relative;
            z-index:1;
            max-width:430px;
        }

        .gov-auth-form{
            width:100%;
        }

        .gov-alert{
            border:1px solid rgba(255,255,255,.08);
            border-radius:16px;
            padding:13px 14px;
            font-size:.92rem;
            line-height:1.55;
            backdrop-filter:blur(10px);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.04),
                0 10px 24px rgba(0,0,0,.16);
        }

        .alert-danger.gov-alert{
            background:linear-gradient(135deg, rgba(255,83,104,.18), rgba(120,20,35,.16));
            color:#ffe2e6;
            border-color:rgba(255,107,122,.38);
        }

        .alert-info.gov-alert{
            background:linear-gradient(135deg, rgba(87,199,255,.17), rgba(18,67,115,.18));
            color:#e3f6ff;
            border-color:rgba(87,199,255,.30);
        }

        .gov-auth-main .gov-auth-form .form-label,
        .gov-auth-main .form-label,
        .gov-auth-main label.form-label{
            color:#f8fbff !important;
            margin-bottom:7px;
            font-size:.92rem;
            font-weight:800;
            letter-spacing:.01em;
            opacity:1 !important;
            text-shadow:0 1px 0 rgba(0,0,0,.18);
        }

        .gov-input{
            display:flex;
            align-items:center;
            min-height:50px;
            border-radius:14px;
            background:rgba(255,255,255,.060) !important;
            border:1px solid rgba(255,255,255,.12) !important;
            transition:.18s ease;
            overflow:hidden;
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,.02),
                0 10px 24px rgba(0,0,0,.10);
        }

        .gov-input:hover{
            border-color:rgba(255,255,255,.18) !important;
            background:rgba(255,255,255,.078) !important;
        }

        .gov-input:focus-within{
            border-color:rgba(212,175,55,.65) !important;
            box-shadow:
                0 0 0 4px rgba(212,175,55,.10),
                0 12px 28px rgba(0,0,0,.14);
            background:rgba(255,255,255,.090) !important;
        }

        .gov-input--error{
            border-color:rgba(255,107,122,.65) !important;
            background:rgba(255,107,122,.08) !important;
            box-shadow:0 0 0 3px rgba(255,107,122,.15) !important;
        }

        .gov-input__ico{
            width:46px;
            flex:0 0 46px;
            text-align:center;
            font-size:.92rem;
            opacity:1 !important;
            color:#ffffff !important;
        }

        .gov-input__field{
            border:0 !important;
            background:transparent !important;
            color:#ffffff !important;
            -webkit-text-fill-color:#ffffff !important;
            min-height:50px;
            padding-left:0;
            padding-right:12px;
            box-shadow:none !important;
            font-size:.96rem;
            font-weight:700;
        }

        .gov-input__field::placeholder{
            color:rgba(238,244,255,.72) !important;
            opacity:1 !important;
            font-weight:600;
        }

        .gov-input__field:-webkit-autofill,
        .gov-input__field:-webkit-autofill:hover,
        .gov-input__field:-webkit-autofill:focus{
            -webkit-text-fill-color:#ffffff !important;
            transition:background-color 99999s ease-in-out 0s;
            box-shadow:0 0 0px 1000px transparent inset !important;
        }

        .gov-input--error .gov-input__field{
            color:#ffffff !important;
            -webkit-text-fill-color:#ffffff !important;
        }

        .gov-input--error .gov-input__field::placeholder{
            color:#ffe3e7 !important;
            opacity:1 !important;
        }

        .gov-input--error .gov-input__ico{
            color:#ffd9de !important;
        }

        .gov-input__toggle{
            border:0;
            background:transparent;
            color:#ffffff !important;
            width:46px;
            height:50px;
            flex:0 0 46px;
            cursor:pointer;
            transition:.18s ease;
            opacity:.95;
        }

        .gov-input__toggle:hover{
            background:rgba(255,255,255,.06);
        }

        .gov-auth-actions{
            margin-top:10px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            flex-wrap:wrap;
        }

        .gov-auth-check{
            color:#d4ddec;
            font-size:.88rem;
            font-weight:700;
        }

        .gov-auth-check input{
            accent-color:var(--gov-gold);
        }

        .gov-auth-link{
            color:var(--gov-gold-2);
            font-weight:800;
            font-size:.88rem;
            transition:.18s ease;
        }

        .gov-auth-link:hover{
            color:#fff3c8;
        }

        .gov-auth-btn{
            min-height:48px;
            border-radius:14px;
            font-weight:900;
            letter-spacing:.02em;
            border-width:1px;
            transition:.18s ease;
            font-size:.96rem;
        }

        .gov-auth-btn.btn-primary{
            background:linear-gradient(135deg, #cfa62b, #f0c85a);
            border-color:rgba(240,200,90,.46);
            color:#1a1a1a;
            box-shadow:0 10px 20px rgba(212,175,55,.15);
        }

        .gov-auth-btn.btn-primary:hover{
            transform:translateY(-1px);
            filter:brightness(1.03);
        }

        .gov-auth-btn.btn-outline-secondary{
            border-color:rgba(255,255,255,.10);
            color:#eef4ff;
            background:rgba(255,255,255,.024);
        }

        .gov-auth-btn.btn-outline-secondary:hover{
            background:rgba(255,255,255,.06);
            border-color:rgba(255,255,255,.16);
            color:#fff;
        }

        .gov-auth-mini{
            color:#aebad0;
            font-size:.80rem;
            line-height:1.48;
            font-weight:600;
        }

        .gov-field-error{
            margin-top:8px;
            display:flex;
            align-items:flex-start;
            gap:8px;
            padding:9px 11px;
            border-radius:12px;
            background:linear-gradient(135deg, rgba(255,83,104,.12), rgba(110,24,36,.10));
            border:1px solid rgba(255,107,122,.25);
            color:var(--gov-red-soft);
            font-size:.82rem;
            line-height:1.45;
            box-shadow:inset 0 1px 0 rgba(255,255,255,.03);
        }

        .gov-field-error__icon{
            flex:0 0 auto;
            font-size:.85rem;
            line-height:1.2;
            margin-top:1px;
        }

        .invalid-feedback{
            display:none !important;
        }

        .gov-auto-dismiss,
        .gov-field-error--dismiss{
            animation:govAlertIn .35s ease;
        }

        @keyframes govAlertIn{
            from{
                opacity:0;
                transform:translateY(-8px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        @media (max-width: 1199.98px){
            .gov-auth-grid{
                grid-template-columns:1fr;
            }

            .gov-auth-side{
                display:none;
            }

            .gov-auth-body{
                max-width:100%;
            }

            .gov-auth-card{
                max-width:560px;
                margin:0 auto;
            }
        }

        @media (max-width: 991.98px){
            .gov-topbar-inner,
            .gov-restricted-inner{
                padding-left:14px;
                padding-right:14px;
            }

            .gov-auth-page{
                padding:16px 12px;
            }

            .gov-auth-main{
                padding:20px 18px;
            }

            .gov-auth-title{
                font-size:1.95rem;
            }
        }

        @media (max-width: 767.98px){
            body.gov-body{
                overflow-x:hidden;
            }

            .gov-restricted-inner{
                min-height:auto;
                padding-top:8px;
                padding-bottom:8px;
                align-items:flex-start;
                flex-direction:column;
            }

            .gov-topbar-inner{
                min-height:auto;
                padding-top:10px;
                padding-bottom:10px;
                align-items:flex-start;
                flex-direction:column;
            }

            .gov-auth-actions{
                flex-direction:column;
                align-items:flex-start;
            }

            .gov-auth-card{
                border-radius:18px;
            }

            .gov-auth-main{
                padding:18px 15px;
            }

            .gov-auth-title{
                font-size:1.7rem;
            }

            .gov-auth-subtitle{
                font-size:.90rem;
                line-height:1.48;
            }

            .gov-auth-page{
                min-height:calc(100vh - 110px);
                height:auto;
                overflow:visible;
                align-items:flex-start;
                padding:14px 10px 24px;
            }
        }
    </style>
</head>

<body class="gov-body">

    <div class="gov-cyber-bg" aria-hidden="true">
        <div class="gov-cyber-bg__ring"></div>
        <div class="gov-cyber-bg__ring gov-cyber-bg__ring--left"></div>
        <div class="gov-cyber-bg__hudline"></div>
        <div class="gov-cyber-bg__hudline gov-cyber-bg__hudline--top"></div>
        <div class="gov-cyber-bg__dots"></div>
    </div>

    <div class="gov-restricted-bar">
        <div class="container-fluid gov-restricted-inner">
            <div class="gov-restricted-left">
                <span class="gov-dot"></span>
                <span>Acesso restrito</span>
                <span class="gov-sep">•</span>
                <span>Fivem.bc</span>
                <span class="gov-sep">•</span>
                <span>Auditoria ativa</span>
            </div>

            <div class="gov-restricted-right">
                <span>Sistema oficial</span>
                <span class="gov-sep">•</span>
                <span>{{ now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

    <header class="gov-topbar">
        <div class="container-fluid gov-topbar-inner">
            <div class="d-flex align-items-center gap-3">
                <div class="gov-emblem" aria-hidden="true">
                    <span class="gov-emblem-dot"></span>
                </div>

                <div class="lh-sm">
                    <div class="gov-org-title">GRR • PRF</div>
                    <div class="gov-org-subtitle">Portal Operacional — Autenticação</div>
                </div>
            </div>

            <div class="text-end">
                <div class="gov-meta">
                    <strong>Ambiente restrito</strong>
                    <span class="mx-2">•</span>
                    <span class="text-muted">Login obrigatório</span>
                </div>
            </div>
        </div>

        <div class="gov-topbar-strip"></div>
    </header>

    <main class="gov-auth-page">
        <div class="gov-auth-wrap">
            <div class="gov-auth-card">
                @yield('content')
            </div>
        </div>
    </main>

    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>