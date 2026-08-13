@extends('dashboard.layout.root')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-page {
        font-family: 'Inter', sans-serif;
    }

    .dashboard-page {
        --dashboard-primary: #4f46e5;
        --dashboard-purple: #7c3aed;
        --dashboard-cyan: #0891b2;
        --dashboard-green: #059669;
        --dashboard-orange: #ea580c;
        --dashboard-red: #e11d48;
    }

    .dashboard-card {
        position: relative;
        border: 1px solid rgba(203, 213, 225, 0.85);
        border-radius: 22px;
        background:
            linear-gradient(
                145deg,
                rgba(255, 255, 255, 1),
                rgba(248, 250, 252, 0.96)
            );
        box-shadow:
            0 10px 35px rgba(15, 23, 42, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.9);
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
    }

    .dashboard-card:hover {
        border-color: rgba(99, 102, 241, 0.34);
        transform: translateY(-4px);
        box-shadow:
            0 22px 50px rgba(15, 23, 42, 0.13),
            0 8px 22px rgba(79, 70, 229, 0.08);
    }

    .dashboard-stat-card {
        position: relative;
        min-height: 180px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.72);
        border-radius: 22px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12);
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 25px 55px rgba(15, 23, 42, 0.18);
    }

    .stat-indigo {
        background:
            radial-gradient(
                circle at top right,
                rgba(255, 255, 255, 0.28),
                transparent 33%
            ),
            linear-gradient(135deg, #4338ca, #6366f1);
    }

    .stat-green {
        background:
            radial-gradient(
                circle at top right,
                rgba(255, 255, 255, 0.28),
                transparent 33%
            ),
            linear-gradient(135deg, #047857, #10b981);
    }

    .stat-orange {
        background:
            radial-gradient(
                circle at top right,
                rgba(255, 255, 255, 0.28),
                transparent 33%
            ),
            linear-gradient(135deg, #c2410c, #f59e0b);
    }

    .stat-cyan {
        background:
            radial-gradient(
                circle at top right,
                rgba(255, 255, 255, 0.28),
                transparent 33%
            ),
            linear-gradient(135deg, #0e7490, #06b6d4);
    }

    .attendance-action-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 24px;
        background:
            radial-gradient(
                circle at top right,
                rgba(34, 211, 238, 0.18),
                transparent 34%
            ),
            radial-gradient(
                circle at bottom left,
                rgba(139, 92, 246, 0.22),
                transparent 38%
            ),
            linear-gradient(
                145deg,
                rgba(15, 23, 42, 0.96),
                rgba(30, 41, 59, 0.96)
            );
        box-shadow:
            0 24px 55px rgba(2, 6, 23, 0.28),
            inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .attendance-time-box {
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.07);
        backdrop-filter: blur(10px);
    }

    .attendance-action-button {
        min-height: 48px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        border: none;
        border-radius: 14px;
        padding: 11px 16px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: -0.01em;
        color: #ffffff;
        box-shadow: 0 12px 25px rgba(15, 23, 42, 0.2);
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            filter 0.2s ease;
    }

    .attendance-action-button:hover {
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-2px);
        filter: brightness(1.06);
        box-shadow: 0 16px 32px rgba(15, 23, 42, 0.28);
    }

    .attendance-action-button:active {
        transform: translateY(0);
    }

    .attendance-action-button:disabled,
    .attendance-action-button.disabled {
        cursor: not-allowed;
        opacity: 0.48;
        filter: grayscale(0.25);
        transform: none;
        box-shadow: none;
    }

    .action-check-in {
        background: linear-gradient(135deg, #059669, #10b981);
    }

    .action-check-out {
        background: linear-gradient(135deg, #e11d48, #f43f5e);
    }

    .action-break-start {
        background: linear-gradient(135deg, #d97706, #f59e0b);
    }

    .action-break-end {
        background: linear-gradient(135deg, #2563eb, #4f46e5);
    }

    .action-disabled {
        background: linear-gradient(135deg, #64748b, #475569);
    }

    .dashboard-section-header {
        background:
            linear-gradient(
                90deg,
                rgba(238, 242, 255, 0.88),
                rgba(236, 254, 255, 0.65)
            );
    }

    .dashboard-scroll::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }

    .dashboard-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #818cf8, #06b6d4);
        border-radius: 999px;
    }

    .dashboard-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 999px;
    }

    .attendance-circle {
        padding: 9px;
        background:
            conic-gradient(
                #10b981 var(--attendance-percent),
                #e2e8f0 0
            );
        box-shadow:
            0 14px 35px rgba(16, 185, 129, 0.2),
            inset 0 0 0 1px rgba(255, 255, 255, 0.9);
    }

    .pulse-ring {
        position: relative;
    }

    .pulse-ring::after {
        content: '';
        position: absolute;
        inset: -5px;
        border: 2px solid currentColor;
        border-radius: 999px;
        opacity: 0.22;
        animation: dashboardPulse 1.8s infinite;
    }

    @keyframes dashboardPulse {
        0% {
            transform: scale(0.72);
            opacity: 0.45;
        }

        75%,
        100% {
            transform: scale(1.35);
            opacity: 0;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | High Contrast / Bootstrap Conflict Safe Styles
    |--------------------------------------------------------------------------
    */

    .dashboard-page {
        color: #0f172a;
    }

    .dashboard-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #312e81;
        border-radius: 26px;
        background: linear-gradient(135deg, #0f172a 0%, #172554 52%, #312e81 100%) !important;
        box-shadow: 0 22px 55px rgba(15, 23, 42, 0.28);
        isolation: isolate;
    }

    .dashboard-hero::before {
        content: '';
        position: absolute;
        width: 310px;
        height: 310px;
        right: -95px;
        top: -115px;
        border-radius: 999px;
        background: rgba(99, 102, 241, 0.34);
        filter: blur(10px);
        z-index: -1;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        width: 260px;
        height: 260px;
        left: 38%;
        bottom: -165px;
        border-radius: 999px;
        background: rgba(6, 182, 212, 0.18);
        filter: blur(16px);
        z-index: -1;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255, 255, 255, 0.26);
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.62) !important;
        color: #ffffff !important;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 5px 14px rgba(2, 6, 23, 0.18);
    }

    .hero-office-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(165, 180, 252, 0.62);
        border-radius: 999px;
        background: rgba(67, 56, 202, 0.62) !important;
        color: #ffffff !important;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
    }

    .hero-title {
        color: #ffffff !important;
        text-shadow: 0 2px 5px rgba(2, 6, 23, 0.28);
    }

    .hero-description {
        color: #dbeafe !important;
        font-weight: 500;
    }

    .hero-meta {
        color: #e2e8f0 !important;
        font-weight: 600;
    }

    .my-attendance-panel {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff !important;
        color: #0f172a !important;
        box-shadow: 0 16px 35px rgba(2, 6, 23, 0.24);
    }

    .my-attendance-heading {
        color: #64748b !important;
        font-weight: 800;
    }

    .attendance-icon-box {
        background: #e0e7ff !important;
        color: #3730a3 !important;
        border: 1px solid #c7d2fe;
    }

    .my-time-box {
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #f8fafc !important;
        color: #0f172a !important;
    }

    .my-time-label {
        color: #64748b !important;
        font-weight: 700;
    }

    .my-time-value {
        color: #0f172a !important;
        font-weight: 800;
    }

    .my-break-box {
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #f8fafc !important;
    }

    .my-break-label {
        color: #64748b !important;
        font-weight: 700;
    }

    .attendance-action-card {
        border-color: #334155;
        background:
            radial-gradient(circle at top right, rgba(6, 182, 212, 0.20), transparent 34%),
            radial-gradient(circle at bottom left, rgba(124, 58, 237, 0.24), transparent 38%),
            linear-gradient(145deg, #0f172a, #1e293b) !important;
    }

    .attendance-time-box {
        border-color: #475569;
        background: #111c2f !important;
        backdrop-filter: none;
    }

    .attendance-time-box p:first-child {
        color: #94a3b8 !important;
    }

    .attendance-action-button {
        opacity: 1 !important;
        filter: none !important;
    }

    .attendance-action-button.action-disabled,
    .attendance-action-button.disabled {
        background: #475569 !important;
        color: #e2e8f0 !important;
        opacity: 0.72 !important;
        border: 1px solid #64748b;
    }

    .dashboard-card {
        background: #ffffff !important;
        border-color: #d8e0ea;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.10);
    }

    .dashboard-card h2,
    .dashboard-card h3,
    .dashboard-card .text-gray-900 {
        color: #0f172a !important;
    }

    .dashboard-card .text-gray-500,
    .dashboard-card .text-gray-400 {
        color: #64748b !important;
    }

    .dashboard-page input {
        color: #0f172a !important;
        background: #ffffff !important;
    }

    @media (max-width: 767px) {
        .dashboard-hero {
            border-radius: 20px;
        }

        .my-attendance-panel {
            margin-top: 4px;
        }
    }

    @media (max-width: 640px) {
        .attendance-action-button {
            min-height: 46px;
            padding-left: 12px;
            padding-right: 12px;
            font-size: 12px;
        }

        .dashboard-stat-card {
            min-height: 160px;
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Mobile Top Attendance Actions
    |--------------------------------------------------------------------------
    */

    .mobile-attendance-actions {
        display: none;
    }

    @media (max-width: 767px) {
        .dashboard-page {
            padding-top: 0;
        }

        .mobile-attendance-actions {
            position: sticky;
            top: 0;
            z-index: 60;
            display: block;
            margin: -8px -8px 14px;
            padding: 10px 8px 12px;
            border-bottom: 1px solid #dbe3ee;
            background: rgba(248, 250, 252, 0.96);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .mobile-attendance-actions-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 7px;
        }

        .mobile-attendance-button {
            display: flex;
            min-width: 0;
            min-height: 66px;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            border: 0;
            border-radius: 13px;
            padding: 8px 4px;
            color: #ffffff !important;
            font-size: 10px;
            font-weight: 800;
            line-height: 1.15;
            text-align: center;
            text-decoration: none !important;
            box-shadow: 0 8px 17px rgba(15, 23, 42, 0.16);
        }

        .mobile-attendance-button i {
            font-size: 17px;
        }

        .mobile-attendance-button span {
            display: block;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mobile-attendance-button.mobile-disabled {
            cursor: not-allowed;
            background: linear-gradient(135deg, #64748b, #475569);
            color: #e2e8f0 !important;
            opacity: 0.72;
            box-shadow: none;
        }

        .dashboard-hero {
            margin-top: 0;
        }
    }



    {{-- BIRTHDAY CSS TEMPORARILY DISABLED --}}
    @if(false)
    /* Birthday Celebration */
    .birthday-celebration {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(217, 70, 239, 0.35);
        border-radius: 24px;
        background:
            radial-gradient(circle at top right, rgba(250, 204, 21, 0.30), transparent 32%),
            radial-gradient(circle at bottom left, rgba(236, 72, 153, 0.24), transparent 38%),
            linear-gradient(135deg, #581c87 0%, #7e22ce 45%, #be185d 100%);
        box-shadow: 0 22px 50px rgba(88, 28, 135, 0.24);
        color: #ffffff;
        isolation: isolate;
    }

    .birthday-celebration::before {
        content: '';
        position: absolute;
        width: 230px;
        height: 230px;
        right: -75px;
        top: -95px;
        border-radius: 999px;
        background: rgba(250, 204, 21, 0.20);
        filter: blur(8px);
        z-index: -1;
    }

    .birthday-celebration::after {
        content: '';
        position: absolute;
        width: 190px;
        height: 190px;
        left: 28%;
        bottom: -130px;
        border-radius: 999px;
        background: rgba(34, 211, 238, 0.16);
        filter: blur(14px);
        z-index: -1;
    }

    .birthday-main-icon {
        display: flex;
        width: 64px;
        height: 64px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.24);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.14);
        color: #fde68a;
        font-size: 28px;
        box-shadow: 0 14px 30px rgba(30, 41, 59, 0.20);
    }

    .birthday-employee-card {
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 17px;
        background: rgba(255, 255, 255, 0.12);
        padding: 14px;
        backdrop-filter: blur(12px);
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .birthday-employee-card:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.18);
    }

    .birthday-avatar {
        display: flex;
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, 0.65);
        border-radius: 15px;
        background: linear-gradient(135deg, #f59e0b, #ec4899);
        color: #ffffff;
        font-size: 13px;
        font-weight: 900;
    }

    .birthday-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .birthday-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 1px solid rgba(255, 255, 255, 0.26);
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.22);
        padding: 7px 12px;
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
    }

    @media (max-width: 640px) {
        .birthday-celebration {
            border-radius: 20px;
        }

        .birthday-main-icon {
            width: 54px;
            height: 54px;
            border-radius: 17px;
            font-size: 23px;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Premium Birthday Popup
    |--------------------------------------------------------------------------
    */
    body.birthday-popup-open {
        overflow: hidden;
    }

    .birthday-popup-overlay {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background:
            radial-gradient(circle at 15% 20%, rgba(236,72,153,.24), transparent 30%),
            radial-gradient(circle at 85% 15%, rgba(250,204,21,.22), transparent 28%),
            rgba(15,23,42,.78);
        opacity: 0;
        visibility: hidden;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        transition: opacity .35s ease, visibility .35s ease;
    }

    .birthday-popup-overlay.is-visible {
        opacity: 1;
        visibility: visible;
    }

    .birthday-popup-dialog {
        position: relative;
        width: min(720px, 100%);
        max-height: calc(100dvh - 36px);
        overflow-x: hidden;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.45) transparent;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 30px;
        background:
            radial-gradient(circle at top right, rgba(250,204,21,.26), transparent 34%),
            radial-gradient(circle at bottom left, rgba(34,211,238,.18), transparent 38%),
            linear-gradient(145deg, #4c1d95 0%, #7e22ce 48%, #be185d 100%);
        box-shadow: 0 35px 90px rgba(15,23,42,.45);
        color: #fff;
        transform: translateY(28px) scale(.94);
        transition: transform .42s cubic-bezier(.2,.9,.2,1);
        isolation: isolate;
    }

    .birthday-popup-overlay.is-visible .birthday-popup-dialog {
        transform: translateY(0) scale(1);
    }

    .birthday-popup-dialog::-webkit-scrollbar {
        width: 7px;
        height: 0;
    }

    .birthday-popup-dialog::-webkit-scrollbar-track {
        background: transparent;
    }

    .birthday-popup-dialog::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(255,255,255,.42);
    }

    .birthday-popup-dialog::before {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        right: -90px;
        top: -105px;
        border-radius: 999px;
        background: rgba(250,204,21,.20);
        filter: blur(4px);
        z-index: -1;
    }

    .birthday-popup-dialog::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        left: -95px;
        bottom: -120px;
        border-radius: 999px;
        background: rgba(34,211,238,.16);
        filter: blur(10px);
        z-index: -1;
    }

    .birthday-popup-close {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 10;
        display: inline-flex;
        width: 42px;
        height: 42px;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.28);
        border-radius: 14px;
        background: rgba(15,23,42,.28);
        color: #fff;
        font-size: 18px;
        cursor: pointer;
        transition: transform .2s ease, background .2s ease;
    }

    .birthday-popup-close:hover {
        transform: rotate(8deg) scale(1.05);
        background: rgba(15,23,42,.48);
    }

    .birthday-popup-content {
        position: relative;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
        padding: 34px 26px 26px;
        text-align: center;
    }

    .birthday-popup-cake {
        display: flex;
        width: 92px;
        height: 92px;
        margin: 0 auto;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(255,255,255,.45);
        border-radius: 28px;
        background: rgba(255,255,255,.15);
        font-size: 50px;
        box-shadow: 0 20px 38px rgba(30,41,59,.24);
        animation: birthdayCakeFloat 2.4s ease-in-out infinite;
    }

    @keyframes birthdayCakeFloat {
        0%, 100% { transform: translateY(0) rotate(-2deg); }
        50% { transform: translateY(-8px) rotate(2deg); }
    }

    .birthday-popup-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 18px;
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 999px;
        background: rgba(15,23,42,.18);
        padding: 7px 13px;
        color: #fde68a;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .birthday-popup-title {
        margin-top: 14px;
        color: #fff !important;
        font-size: clamp(28px, 5vw, 44px);
        font-weight: 900;
        line-height: 1.08;
        text-shadow: 0 4px 16px rgba(30,41,59,.28);
    }

    .birthday-popup-message {
        max-width: 560px;
        margin: 12px auto 0;
        color: #f5e8ff;
        font-size: 15px;
        font-weight: 600;
        line-height: 1.7;
    }

    .birthday-popup-person {
        margin-top: 20px;
        border: 1px solid rgba(255,255,255,.24);
        border-radius: 22px;
        background: rgba(255,255,255,.12);
        padding: 16px;
        text-align: left;
        backdrop-filter: blur(8px);
    }

    .birthday-popup-avatar {
        display: flex;
        width: 62px;
        height: 62px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 3px solid rgba(255,255,255,.74);
        border-radius: 20px;
        background: linear-gradient(135deg,#f59e0b,#ec4899);
        color: #fff;
        font-size: 17px;
        font-weight: 900;
        box-shadow: 0 12px 24px rgba(30,41,59,.22);
    }

    .birthday-popup-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .birthday-popup-team-list {
        margin-top: 18px;
        display: grid;
        gap: 10px;
        max-height: 245px;
        overflow-y: auto;
        padding-right: 3px;
    }

    .birthday-popup-team-item {
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid rgba(255,255,255,.20);
        border-radius: 17px;
        background: rgba(15,23,42,.16);
        padding: 12px;
        text-align: left;
    }

    .birthday-popup-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    .birthday-popup-button {
        display: inline-flex;
        min-height: 46px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 14px;
        padding: 11px 18px;
        font-size: 13px;
        font-weight: 900;
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .birthday-popup-button:hover {
        transform: translateY(-2px);
    }

    .birthday-popup-button-primary {
        background: linear-gradient(135deg,#facc15,#fb7185);
        color: #4c1d95;
        box-shadow: 0 14px 28px rgba(250,204,21,.20);
    }

    .birthday-popup-button-secondary {
        border: 1px solid rgba(255,255,255,.26);
        background: rgba(255,255,255,.12);
        color: #fff;
    }

    .birthday-confetti {
        position: absolute;
        top: -20px;
        width: 10px;
        height: 16px;
        border-radius: 3px;
        opacity: .92;
        pointer-events: none;
        animation: birthdayConfettiFall linear forwards;
    }

    @keyframes birthdayConfettiFall {
        from {
            transform: translate3d(0,-20px,0) rotate(0deg);
            opacity: 1;
        }
        to {
            transform: translate3d(var(--confetti-x),110vh,0) rotate(760deg);
            opacity: 0;
        }
    }

    @media (max-width: 640px) {
        .birthday-popup-overlay {
            padding: 10px;
        }

        .birthday-popup-dialog {
            max-height: calc(100dvh - 20px);
            border-radius: 23px;
        }

        .birthday-popup-content {
            padding: 30px 16px 20px;
        }

        .birthday-popup-cake {
            width: 78px;
            height: 78px;
            border-radius: 23px;
            font-size: 42px;
        }

        .birthday-popup-person {
            padding: 13px;
        }

        .birthday-popup-actions {
            flex-direction: column;
        }

        .birthday-popup-button {
            width: 100%;
        }
    }

    @endif


    /*
    |--------------------------------------------------------------------------
    | Temporary Independence Day Theme (13 Aug 2026 - 15 Aug 2026)
    |--------------------------------------------------------------------------
    | 16 Aug 2026 se ye classes automatically apply nahi hongi.
    */
    .dashboard-page.independence-theme {
        --india-saffron: #ff8c1a;
        --india-green: #138808;
        --india-navy: #000080;
        --india-white: #ffffff;
    }

    .dashboard-page.independence-theme .dashboard-hero {
        border-color: rgba(255, 255, 255, 0.70);
        background:
            radial-gradient(circle at 12% 15%, rgba(255, 153, 51, 0.40), transparent 30%),
            radial-gradient(circle at 88% 85%, rgba(19, 136, 8, 0.34), transparent 32%),
            linear-gradient(135deg, #ff9933 0%, #ffffff 48%, #f8fafc 55%, #138808 100%) !important;
        box-shadow: 0 22px 55px rgba(15, 23, 42, 0.20);
    }

    .dashboard-page.independence-theme .dashboard-hero::before {
        width: 220px;
        height: 220px;
        right: -55px;
        top: -70px;
        border: 18px solid rgba(0, 0, 128, 0.18);
        background: transparent;
        filter: none;
    }

    .dashboard-page.independence-theme .dashboard-hero::after {
        left: auto;
        right: 52px;
        bottom: -105px;
        width: 210px;
        height: 210px;
        background: rgba(255, 255, 255, 0.28);
        filter: blur(8px);
    }

    .dashboard-page.independence-theme .hero-title {
        color: #0f172a !important;
        text-shadow: 0 1px 0 rgba(255,255,255,.75);
    }

    .dashboard-page.independence-theme .hero-description,
    .dashboard-page.independence-theme .hero-meta {
        color: #1e293b !important;
    }

    .dashboard-page.independence-theme .hero-badge {
        border-color: rgba(255, 255, 255, 0.92);
        background: rgba(255, 255, 255, 0.78) !important;
        color: #0f172a !important;
        backdrop-filter: blur(10px);
    }

    .dashboard-page.independence-theme .hero-office-badge {
        border-color: rgba(0, 0, 128, 0.18);
        background: rgba(255, 255, 255, 0.78) !important;
        color: #000080 !important;
        backdrop-filter: blur(10px);
    }

    .dashboard-page.independence-theme .my-attendance-panel {
        border-color: rgba(255,255,255,.88);
        background: rgba(255,255,255,.92) !important;
        backdrop-filter: blur(10px);
    }

    .independence-mini-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(0, 0, 128, 0.14);
        border-radius: 999px;
        background: rgba(255,255,255,.86);
        padding: 7px 12px;
        color: #000080;
        font-size: 12px;
        font-weight: 900;
        box-shadow: 0 8px 20px rgba(15,23,42,.08);
    }

    .independence-wish-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.90);
        border-radius: 24px;
        background:
            radial-gradient(circle at 8% 20%, rgba(255,153,51,.26), transparent 28%),
            radial-gradient(circle at 92% 82%, rgba(19,136,8,.24), transparent 30%),
            linear-gradient(135deg, #fff7ed 0%, #ffffff 48%, #f0fdf4 100%);
        box-shadow: 0 18px 45px rgba(15,23,42,.12);
        isolation: isolate;
    }

    .independence-wish-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, #ff9933 0 33.33%, #ffffff 33.33% 66.66%, #138808 66.66% 100%);
    }

    .independence-wheel {
        display: flex;
        width: 66px;
        height: 66px;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
        border: 4px solid #000080;
        border-radius: 999px;
        background: #ffffff;
        color: #000080;
        font-size: 28px;
        box-shadow: 0 12px 28px rgba(0,0,128,.14);
    }

    .independence-wish-title {
        background: linear-gradient(90deg, #e76f00, #000080 50%, #138808);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    @media (max-width: 640px) {
        .independence-wish-card {
            border-radius: 20px;
        }

        .independence-wheel {
            width: 56px;
            height: 56px;
            font-size: 24px;
        }
    }

</style>
@endpush

@section('content')

@php
    use Illuminate\Support\Carbon;

    $today = now();

    /*
    |--------------------------------------------------------------------------
    | Temporary Independence Day Theme
    |--------------------------------------------------------------------------
    | Theme: 13 Aug 2026 to 15 Aug 2026 (inclusive)
    | Special wish: only on 15 Aug 2026
    | 16 Aug 2026 onwards: normal dashboard automatically
    */
    $independenceThemeStart = Carbon::create(2026, 8, 13)->startOfDay();
    $independenceThemeEnd = Carbon::create(2026, 8, 15)->endOfDay();
    $independenceDay = Carbon::create(2026, 8, 15)->startOfDay();

    $isIndependenceTheme = $today->between(
        $independenceThemeStart,
        $independenceThemeEnd
    );

    $isIndependenceDay = $today->isSameDay($independenceDay);

    $attendanceMap = $todayCheckIn->keyBy('user_id');

    $leaveUserIds = $leaves
        ->pluck('user_id')
        ->unique();

    $activeOffice = $offices
        ->firstWhere('id', $activeOfficeId);

    $totalEmployees = $totalEmployees
        ?? $employees->count();

    $presentEmployees = $presentEmployees
        ?? $todayCheckIn
            ->pluck('user_id')
            ->unique()
            ->count();

    $onLeaveEmployees = $onLeaveEmployees
        ?? $leaveUserIds->count();

    $absentEmployees = $absentEmployees
        ?? max(
            0,
            $totalEmployees -
            $presentEmployees -
            $onLeaveEmployees
        );

    $attendancePercentage = $attendancePercentage
        ?? (
            $totalEmployees > 0
                ? round(
                    ($presentEmployees / $totalEmployees) * 100
                )
                : 0
        );

    $currentlyWorkingEmployees = $currentlyWorkingEmployees
        ?? $todayCheckIn
            ->filter(function ($attendance) {
                return empty($attendance->check_out);
            })
            ->count();

    $checkedOutEmployees = $checkedOutEmployees
        ?? $todayCheckIn
            ->filter(function ($attendance) {
                return !empty($attendance->check_out);
            })
            ->count();

    $lateEmployees = $lateEmployees ?? 0;

    $totalLastMonthPayout = $totalLastMonthPayout
        ?? $lastMonthPayouts->sum(function ($salary) {
            return (float) (
                $salary->net_salary
                ?? $salary->paid_amount
                ?? $salary->amount
                ?? 0
            );
        });

    $presentPercentage = $totalEmployees > 0
        ? round(($presentEmployees / $totalEmployees) * 100)
        : 0;

    $leavePercentage = $totalEmployees > 0
        ? round(($onLeaveEmployees / $totalEmployees) * 100)
        : 0;

    $absentPercentage = $totalEmployees > 0
        ? round(($absentEmployees / $totalEmployees) * 100)
        : 0;

    $myAttendanceStatus = 'Not Checked In';
    $myAttendanceBadge = 'bg-red-100 text-red-700';
    $myAttendanceDot = 'bg-red-500';

    if ($todayAttendanceRecord) {
        if (!empty($todayAttendanceRecord->check_out)) {
            $myAttendanceStatus = 'Checked Out';
            $myAttendanceBadge = 'bg-gray-100 text-gray-700';
            $myAttendanceDot = 'bg-gray-500';
        } else {
            $myAttendanceStatus = 'Currently Working';
            $myAttendanceBadge = 'bg-green-100 text-green-700';
            $myAttendanceDot = 'bg-green-500';
        }
    }

    $breakStatus = 'No Break Started';
    $breakBadge = 'bg-gray-100 text-gray-600';

    if ($break) {
        if (!empty($break->end_time)) {
            $breakStatus = 'Break Completed';
            $breakBadge = 'bg-blue-100 text-blue-700';
        } else {
            $breakStatus = 'Currently on Break';
            $breakBadge = 'bg-yellow-100 text-yellow-700';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Logged-in User Attendance Actions
    |--------------------------------------------------------------------------
    */
    // $hasCheckedIn = !empty($todayAttendanceRecord?->check_in);
    // $hasCheckedOut = !empty($todayAttendanceRecord?->check_out);
    // $hasOpenBreak = $break && empty($break->end_time);

    // $canCheckIn = !$hasCheckedIn;
    // $canCheckOut = $hasCheckedIn && !$hasCheckedOut;
    // $canUseBreak = $hasCheckedIn && !$hasCheckedOut;


  

    $hasCheckedIn = !empty($todayAttendanceRecord?->check_in);

    $hasCheckedOut = !empty(
        $todayAttendanceRecord?->check_out
    );

    $hasOpenBreak = $break &&
        empty($break->end_time);

    $canCheckIn = !$hasCheckedIn;


    $canCheckOut = $hasCheckedIn;

    $canUseBreak =
        $hasCheckedIn &&
        !$hasCheckedOut;
@endphp

<div class="dashboard-page {{ $isIndependenceTheme ? 'independence-theme' : '' }} space-y-6 pb-10">


    {{-- 15 August Special Independence Day Wish --}}
    @if($isIndependenceDay)
        <section class="independence-wish-card p-5 sm:p-6">
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <div class="independence-wheel" aria-hidden="true">
                        <i class="fas fa-dharmachakra"></i>
                    </div>

                    <div>
                        <span class="independence-mini-badge">
                            🇮🇳 15 August 2026
                        </span>

                        <h2 class="independence-wish-title mt-3 text-2xl font-black tracking-tight sm:text-3xl">
                            Happy Independence Day! 🇮🇳
                        </h2>

                        <p class="mt-2 max-w-3xl text-sm font-semibold leading-6 text-slate-700 sm:text-base">
                            स्वतंत्रता दिवस की हार्दिक शुभकामनाएं। आइए हम सब मिलकर
                            स्वतंत्रता, एकता और देश की प्रगति के मूल्यों का सम्मान करें।
                        </p>
                    </div>
                </div>

                <div class="shrink-0 text-center sm:text-right">
                    <div class="text-5xl sm:text-6xl" aria-hidden="true">🇮🇳</div>
                    <p class="mt-2 text-xs font-black uppercase tracking-[0.20em] text-blue-900">
                        Jai Hind
                    </p>
                </div>
            </div>
        </section>
    @endif


    {{-- BIRTHDAY PERSONAL POPUP TEMPORARILY DISABLED --}}
    @if(false)
    {{-- Premium Birthday Popup: sirf logged-in birthday employee ko personal wish --}}
    @if($isUserBirthdayToday)
        @php
            $birthdayOfficeName = optional($user->office)->name
                ?? $activeOffice?->name
                ?? 'आपके कार्यालय';

            $loggedInBirthdayInitials = collect(explode(' ', $user->name ?? 'User'))
                ->filter()
                ->take(2)
                ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
                ->implode('');

            $loggedInBirthdayPhoto = !empty($user->photo)
                ? asset('storage/' . $user->photo)
                : null;
        @endphp

        <div
            id="birthday-popup"
            class="birthday-popup-overlay"
            role="dialog"
            aria-modal="true"
            aria-labelledby="birthday-popup-title"
            aria-hidden="true"
        >
            <div class="birthday-popup-dialog" role="document">
                <button
                    type="button"
                    class="birthday-popup-close"
                    id="birthday-popup-close"
                    aria-label="Close birthday popup"
                >
                    <i class="fas fa-times"></i>
                </button>

                <div class="birthday-popup-content">
                    <div class="birthday-popup-cake">🎂</div>

                    <span class="birthday-popup-kicker">
                        <i class="fas fa-star"></i>
                        Today Is Your Special Day
                    </span>

                    <h2 id="birthday-popup-title" class="birthday-popup-title">
                        Happy Birthday, {{ $user->name }}! 🎉
                    </h2>

                    <p class="birthday-popup-message">
                        आपके जन्मदिन पर हम आपके अच्छे स्वास्थ्य, सफलता और खुशियों की
                        कामना करते हैं। <strong>{{ $birthdayOfficeName }}</strong> की ओर से
                        आपको जन्मदिन की ढेरों शुभकामनाएं!
                    </p>

                    <div class="birthday-popup-person">
                        <div class="flex items-center gap-4">
                            <div class="birthday-popup-avatar">
                                @if($loggedInBirthdayPhoto)
                                    <img
                                        src="{{ $loggedInBirthdayPhoto }}"
                                        alt="{{ $user->name }}"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                    >
                                    <span class="hidden h-full w-full items-center justify-center">
                                        {{ $loggedInBirthdayInitials ?: 'U' }}
                                    </span>
                                @else
                                    {{ $loggedInBirthdayInitials ?: 'U' }}
                                @endif
                            </div>

                            <div class="min-w-0 flex-1">
                                <p class="truncate text-lg font-black text-white">
                                    {{ $user->name }}
                                </p>

                                <p class="mt-1 text-sm font-semibold text-purple-100">
                                    {{ $user->designation
                                        ?? optional($user->department)->name
                                        ?? optional($user->office)->name
                                        ?? 'Team Member'
                                    }}
                                </p>

                                @if($userBirthdayAge !== null)
                                    <p class="mt-2 inline-flex items-center gap-2 text-xs font-extrabold text-yellow-200">
                                        <i class="fas fa-gift"></i>
                                        Celebrating {{ $userBirthdayAge }} wonderful years
                                    </p>
                                @endif
                            </div>

                            <div class="shrink-0 text-4xl">🎁</div>
                        </div>
                    </div>

                    <div class="birthday-popup-actions">
                        <button
                            type="button"
                            id="birthday-celebrate-button"
                            class="birthday-popup-button birthday-popup-button-primary"
                        >
                            <i class="fas fa-birthday-cake"></i>
                            Celebrate
                        </button>

                        <button
                            type="button"
                            id="birthday-popup-done"
                            class="birthday-popup-button birthday-popup-button-secondary"
                        >
                            <i class="fas fa-check"></i>
                            Thank You
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @endif


    {{-- Mobile Top Attendance Actions --}}
    <section class="mobile-attendance-actions">
        <div class="mobile-attendance-actions-grid">

            {{-- Mobile Check In --}}
            @if($canCheckIn)
                <a
                    href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                    class="mobile-attendance-button action-check-in"
                >
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Check In</span>
                </a>
            @else
                <span
                    class="mobile-attendance-button mobile-disabled"
                    title="You have already checked in today"
                >
                    <i class="fas fa-check-circle"></i>
                    <span>Checked In</span>
                </span>
            @endif

            {{-- Mobile Check Out --}}
            @if($canCheckOut)
                <a
                    href="{{ route('attendance.form', ['form_type' => 'check_out']) }}"
                    class="mobile-attendance-button action-check-out"
                    title="{{ $hasCheckedOut
                        ? 'Update checkout time again'
                        : 'Mark your checkout'
                    }}"
                >
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Check Out</span>
                </a>
            @else
                <span
                    class="mobile-attendance-button mobile-disabled"
                    title="Please check in first"
                >
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Check Out</span>
                </span>
            @endif

            {{-- Mobile Break Start / End --}}
            @if($canUseBreak && $hasOpenBreak)
                <a
                    href="{{ route('break.form', [
                        'employee' => auth()->id(),
                        'break' => $break->id
                    ]) }}"
                    class="mobile-attendance-button action-break-end"
                >
                    <i class="fas fa-stop-circle"></i>
                    <span>Stop Break</span>
                </a>
            @elseif($canUseBreak)
                <a
                    href="{{ route('break.form') }}"
                    class="mobile-attendance-button action-break-start"
                >
                    <i class="fas fa-coffee"></i>
                    <span>Take Break</span>
                </a>
            @else
                <span
                    class="mobile-attendance-button mobile-disabled"
                    title="Break is available after check in and before check out"
                >
                    <i class="fas fa-coffee"></i>
                    <span>Take Break</span>
                </span>
            @endif

            {{-- Mobile Attendance Records --}}
            <a
                href="{{ route('attendance.index') }}"
                class="mobile-attendance-button bg-gradient-to-br from-violet-600 to-indigo-600"
            >
                <i class="fas fa-calendar-check"></i>
                <span>My Records</span>
            </a>
        </div>
    </section>

    {{-- Header --}}
    <section class="dashboard-hero p-6 sm:p-8">

        <div class="relative grid grid-cols-1 gap-6 xl:grid-cols-3 xl:items-center">

            <div class="xl:col-span-2">

                <div class="flex flex-wrap items-center gap-2">

                    <span class="hero-badge">

                        <span class="h-2 w-2 animate-pulse rounded-full bg-green-400"></span>

                        {{ $isIndependenceTheme ? 'Independence Week Dashboard' : 'Live Workforce Dashboard' }}
                    </span>

                    @if($activeOffice)
                        <span class="hero-office-badge">

                            <i class="fas fa-building text-xs"></i>

                            {{ $activeOffice->name }}
                        </span>
                    @endif
                </div>

                <h1 class="hero-title mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">

                    @if($isIndependenceDay)
                        Happy Independence Day, {{ auth()->user()->name }}! 🇮🇳
                    @else
                        Good
                        {{ now()->hour < 12
                            ? 'Morning'
                            : (
                                now()->hour < 17
                                    ? 'Afternoon'
                                    : 'Evening'
                            )
                        }},

                        {{ auth()->user()->name }}
                    @endif
                </h1>

                {{-- <p class="hero-description mt-3 max-w-2xl text-sm leading-6 sm:text-base">

                    @if($isIndependenceTheme)
                        🇮🇳 स्वतंत्रता दिवस के उत्सव के साथ attendance, employees, leaves, offices और salary activity को एक ही dashboard से monitor करें।
                    @else
                        Attendance, employees, leaves, offices and salary activity को एक ही dashboard से monitor करें।
                    @endif
                </p> --}}

                <div class="hero-meta mt-5 flex flex-wrap items-center gap-5 text-sm">

                    <span class="inline-flex items-center gap-2">

                        <i class="far fa-calendar-alt text-indigo-300"></i>

                        {{ $today->format('l, d F Y') }}
                    </span>

                    <span class="inline-flex items-center gap-2">

                        <i class="far fa-clock text-cyan-300"></i>

                        <span id="dashboard-live-clock">
                            {{ now()->format('h:i:s A') }}
                        </span>
                    </span>
                </div>
            </div>

            {{-- My Attendance --}}
            <div class="my-attendance-panel p-5">

                <div class="flex items-start justify-between">

                    <div>
                        <p class="my-attendance-heading text-xs uppercase tracking-wider">
                            Your Attendance
                        </p>

                        <span class="mt-3 inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $myAttendanceBadge }}">

                            <span class="h-2 w-2 rounded-full {{ $myAttendanceDot }}"></span>

                            {{ $myAttendanceStatus }}
                        </span>
                    </div>

                    <div class="attendance-icon-box flex h-11 w-11 items-center justify-center rounded-xl">

                        <i class="fas fa-user-check text-lg"></i>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">

                    <div class="my-time-box p-3">

                        <p class="my-time-label text-xs">
                            Check In
                        </p>

                        <p class="my-time-value mt-1 text-sm">

                            {{ $todayAttendanceRecord?->check_in
                                ? Carbon::parse(
                                    $todayAttendanceRecord->check_in
                                )->format('h:i A')
                                : '--:--'
                            }}
                        </p>
                    </div>

                    <div class="my-time-box p-3">

                        <p class="my-time-label text-xs">
                            Check Out
                        </p>

                        <p class="my-time-value mt-1 text-sm">

                            {{ $todayAttendanceRecord?->check_out
                                ? Carbon::parse(
                                    $todayAttendanceRecord->check_out
                                )->format('h:i A')
                                : '--:--'
                            }}
                        </p>
                    </div>
                </div>

                <div class="my-break-box mt-3 flex items-center justify-between px-3 py-3">

                    <span class="my-break-label text-xs">
                        Break Status
                    </span>

                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $breakBadge }}">
                        {{ $breakStatus }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- BIRTHDAY TEAM CELEBRATION TEMPORARILY DISABLED --}}
    @if(false)
    @php
        /*
        |--------------------------------------------------------------------------
        | Other Birthday Employees
        |--------------------------------------------------------------------------
        | Logged-in employee को team birthday list से हटा दिया गया है।
        | इसलिए अपना birthday होने पर अपनी ही team card दिखाई नहीं देगी।
        */
        $otherBirthdayEmployees = $todayBirthdayEmployees
            ->reject(function ($birthdayEmployee) {
                return (int) $birthdayEmployee->id === (int) auth()->id();
            })
            ->values();
    @endphp

    {{-- Birthday Celebration --}}
    @if($isUserBirthdayToday || $otherBirthdayEmployees->isNotEmpty())
        <section class="birthday-celebration p-5 sm:p-6">
            <div class="relative">

                {{--
                |--------------------------------------------------------------------------
                | Logged-in User Personal Birthday Wish
                |--------------------------------------------------------------------------
                | यह सिर्फ उसी employee को दिखेगा जिसका आज birthday है।
                --}}
                @if($isUserBirthdayToday)
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-start gap-4">

                            <div class="birthday-main-icon">
                                <i class="fas fa-birthday-cake"></i>
                            </div>

                            <div>
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1.5 text-xs font-extrabold text-yellow-200">
                                    <i class="fas fa-star"></i>
                                    Today Is Your Special Day
                                </span>

                                <h2 class="mt-3 text-2xl font-extrabold text-white sm:text-3xl">
                                    Happy Birthday, {{ $user->name }}! 🎉
                                </h2>

                                <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-purple-100 sm:text-base">
                                    आपको जन्मदिन की हार्दिक शुभकामनाएं। आपका आने वाला वर्ष
                                    खुशियों, सफलता और अच्छे स्वास्थ्य से भरा रहे।
                                </p>

                                @if($userBirthdayAge !== null)
                                    <p class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-yellow-200">
                                        <i class="fas fa-gift"></i>

                                        Celebrating {{ $userBirthdayAge }} wonderful years
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="hidden shrink-0 lg:block">
                            <div class="flex h-28 w-28 items-center justify-center rounded-full border border-white/20 bg-white/10 text-6xl shadow-xl">
                                🎂
                            </div>
                        </div>
                    </div>
                @endif


                {{--
                |--------------------------------------------------------------------------
                | Other Team Members Birthday
                |--------------------------------------------------------------------------
                | यह section तभी दिखेगा जब logged-in employee के अलावा
                | किसी दूसरे employee का birthday हो।
                --}}
                @if($otherBirthdayEmployees->isNotEmpty())

                    @if($isUserBirthdayToday)
                        <div class="my-6 border-t border-white/20"></div>
                    @endif

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs font-extrabold uppercase tracking-widest text-yellow-200">
                                Team Birthday Celebration
                            </p>

                            <h3 class="mt-1 text-xl font-extrabold text-white">
                                आज इन Team Members का Birthday है 🎈
                            </h3>
                        </div>

                        <span class="birthday-count-badge">
                            <i class="fas fa-birthday-cake text-yellow-300"></i>

                            {{ $otherBirthdayEmployees->count() }}

                            {{ \Illuminate\Support\Str::plural(
                                'Birthday',
                                $otherBirthdayEmployees->count()
                            ) }}
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">

                        @foreach($otherBirthdayEmployees as $birthdayEmployee)
                            @php
                                $birthdayInitials = collect(
                                    explode(' ', $birthdayEmployee->name ?? 'User')
                                )
                                    ->filter()
                                    ->take(2)
                                    ->map(function ($part) {
                                        return strtoupper(
                                            mb_substr($part, 0, 1)
                                        );
                                    })
                                    ->implode('');

                                $birthdayPhoto = !empty($birthdayEmployee->photo)
                                    ? asset('storage/' . $birthdayEmployee->photo)
                                    : null;

                                $birthdayEmployeeOfficeName =
                                    optional($birthdayEmployee->office)->name
                                    ?? 'Office Team';
                            @endphp

                            <article class="birthday-employee-card">

                                <div class="flex items-center gap-3">

                                    <div class="birthday-avatar">
                                        @if($birthdayPhoto)
                                            <img
                                                src="{{ $birthdayPhoto }}"
                                                alt="{{ $birthdayEmployee->name }}"
                                                onerror="
                                                    this.style.display='none';
                                                    this.nextElementSibling.style.display='flex';
                                                "
                                            >

                                            <span class="hidden h-full w-full items-center justify-center">
                                                {{ $birthdayInitials ?: 'U' }}
                                            </span>
                                        @else
                                            {{ $birthdayInitials ?: 'U' }}
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-extrabold text-white">
                                            {{ $birthdayEmployee->name }}
                                        </p>

                                        <p class="mt-1 truncate text-xs font-semibold text-purple-100">
                                            {{
                                                $birthdayEmployee->designation
                                                ?? optional($birthdayEmployee->department)->name
                                                ?? $birthdayEmployeeOfficeName
                                                ?? 'Team Member'
                                            }}
                                        </p>

                                        @if($birthdayEmployee->birthday_age !== null)
                                            <p class="mt-1 text-xs font-bold text-yellow-200">
                                                Turns
                                                {{ $birthdayEmployee->birthday_age }}
                                                today
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-yellow-300/20 text-xl">
                                        🎁
                                    </div>
                                </div>

                                <div class="mt-4 rounded-xl border border-white/15 bg-slate-950/15 px-3 py-3">
                                    <p class="text-xs font-semibold leading-5 text-white">
                                        आज

                                        <span class="font-extrabold">
                                            {{ $birthdayEmployee->name }}
                                        </span>

                                        का जन्मदिन है। पूरी

                                        <span class="font-extrabold">
                                            {{ $birthdayEmployeeOfficeName }}
                                        </span>

                                        की ओर से जन्मदिन की हार्दिक शुभकामनाएं! 🎉
                                    </p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif
    @endif


    {{-- Attendance Quick Actions --}}
    <section class="attendance-action-card p-5 sm:p-6">
        <div class="relative grid grid-cols-1 gap-6 xl:grid-cols-3 xl:items-center">

            <div class="xl:col-span-1">
                <div class="flex items-start gap-4">
                    <div class="pulse-ring flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-cyan-400 bg-opacity-15 text-cyan-300">
                        <i class="fas fa-fingerprint text-xl"></i>
                    </div>

                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-cyan-300">
                            Quick Attendance
                        </p>

                        <h2 class="mt-1 text-xl font-extrabold text-white sm:text-2xl">
                            Mark Your Attendance
                        </h2>

                        <p class="mt-2 text-sm leading-6 text-slate-300">
                            Check in, check out और break को dashboard से ही manage करें।
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 xl:col-span-2">

                {{-- Check In --}}
                @if($canCheckIn)
                    <a href="{{ route('attendance.form', ['form_type' => 'check_in']) }}"
                       class="attendance-action-button action-check-in">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Check In</span>
                    </a>
                @else
                    <span class="attendance-action-button action-disabled disabled"
                          title="You have already checked in today">
                        <i class="fas fa-check-circle"></i>
                        <span>Checked In</span>
                    </span>
                @endif

                {{-- Check Out --}}
                @if($canCheckOut)
                    <a
                        href="{{ route('attendance.form', [
                            'form_type' => 'check_out'
                        ]) }}"
                        class="attendance-action-button action-check-out"
                        title="{{ $hasCheckedOut
                            ? 'Checkout time दोबारा update करें'
                            : 'Mark your checkout'
                        }}"
                    >
                        
                            <i class="fas fa-sign-out-alt"></i>

                            <span>
                                Check Out
                            </span>
                    </a>
                @else
                    <span
                        class="attendance-action-button action-disabled disabled"
                        title="Please check in first"
                    >
                        <i class="fas fa-sign-out-alt"></i>

                        <span>
                            Check Out
                        </span>
                    </span>
                @endif

                {{-- Break Start / End --}}
                @if($canUseBreak && $hasOpenBreak)
                    <a href="{{ route('break.form', ['employee' => auth()->id(), 'break' => $break->id]) }}"
                       class="attendance-action-button action-break-end">
                        <i class="fas fa-play-circle"></i>
                        <span>Stop Break</span>
                    </a>
                @elseif($canUseBreak)
                    <a href="{{ route('break.form') }}"
                       class="attendance-action-button action-break-start">
                        <i class="fas fa-coffee"></i>
                        <span>Take Break</span>
                    </a>
                @else
                    <span class="attendance-action-button action-disabled disabled"
                          title="Break is available after check in and before check out">
                        <i class="fas fa-coffee"></i>
                        <span>Take Break</span>
                    </span>
                @endif

                {{-- Attendance Records --}}
                <a href="{{ route('attendance.index') }}"
                   class="attendance-action-button bg-gradient-to-br from-violet-600 to-indigo-600">
                    <i class="fas fa-calendar-check"></i>
                    <span>My Records</span>
                </a>
            </div>
        </div>

        <div class="relative mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="attendance-time-box flex items-center justify-between px-4 py-3">
                <div>
                    <p class="text-xs font-medium text-slate-400">Today Check In</p>
                    <p class="mt-1 text-sm font-extrabold text-white">
                        {{ $todayAttendanceRecord?->check_in
                            ? Carbon::parse($todayAttendanceRecord->check_in)->format('h:i A')
                            : 'Not marked yet'
                        }}
                    </p>
                </div>
                <i class="fas fa-arrow-right-to-bracket text-emerald-400"></i>
            </div>

            <div class="attendance-time-box flex items-center justify-between px-4 py-3">
                <div>
                    <p class="text-xs font-medium text-slate-400">Today Check Out</p>
                    <p class="mt-1 text-sm font-extrabold text-white">
                        {{ $todayAttendanceRecord?->check_out
                            ? Carbon::parse($todayAttendanceRecord->check_out)->format('h:i A')
                            : 'Not marked yet'
                        }}
                    </p>
                </div>
                <i class="fas fa-arrow-right-from-bracket text-rose-400"></i>
            </div>

            <div class="attendance-time-box flex items-center justify-between px-4 py-3">
                <div>
                    <p class="text-xs font-medium text-slate-400">Current Status</p>
                    <p class="mt-1 text-sm font-extrabold text-white">{{ $myAttendanceStatus }}</p>
                </div>
                <span class="h-3 w-3 rounded-full {{ $myAttendanceDot }}"></span>
            </div>
        </div>
    </section>

    {{-- Summary Cards --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- Employees --}}
        <article class="dashboard-card relative overflow-hidden p-5">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-indigo-50"></div>

            <div class="relative">

                <div class="flex items-center justify-between">

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-600">
                        <i class="fas fa-users text-xl"></i>
                    </div>

                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">

                        {{ $offices->count() }}
                        {{ Str::plural('Office', $offices->count()) }}
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-gray-500">
                    Total Employees
                </p>

                <div class="mt-1 flex items-end justify-between">

                    <h2 class="text-3xl font-extrabold text-gray-900">
                        {{ number_format($totalEmployees) }}
                    </h2>

                    <span class="text-xs font-semibold text-indigo-600">
                        Active Staff
                    </span>
                </div>
            </div>
        </article>

        {{-- Present --}}
        <article class="dashboard-card relative overflow-hidden p-5">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-green-50"></div>

            <div class="relative">

                <div class="flex items-center justify-between">

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-green-100 text-green-600">

                        <i class="fas fa-user-check text-xl"></i>
                    </div>

                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">

                        {{ $attendancePercentage }}%
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-gray-500">
                    Present Today
                </p>

                <div class="mt-1 flex items-end justify-between">

                    <h2 class="text-3xl font-extrabold text-gray-900">
                        {{ number_format($presentEmployees) }}
                    </h2>

                    <span class="text-xs font-semibold text-green-600">

                        {{ $currentlyWorkingEmployees }}
                        Working
                    </span>
                </div>
            </div>
        </article>

        {{-- Leave --}}
        <article class="dashboard-card relative overflow-hidden p-5">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-yellow-50"></div>

            <div class="relative">

                <div class="flex items-center justify-between">

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-yellow-100 text-yellow-600">

                        <i class="fas fa-calendar-minus text-xl"></i>
                    </div>

                    <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700">

                        Approved
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-gray-500">
                    On Leave Today
                </p>

                <div class="mt-1 flex items-end justify-between">

                    <h2 class="text-3xl font-extrabold text-gray-900">
                        {{ number_format($onLeaveEmployees) }}
                    </h2>

                    <span class="text-xs font-semibold text-red-600">

                        {{ $absentEmployees }}
                        Absent
                    </span>
                </div>
            </div>
        </article>

        {{-- Payout --}}
        <article class="dashboard-card relative overflow-hidden p-5">

            <div class="absolute right-0 top-0 h-24 w-24 rounded-bl-full bg-cyan-50"></div>

            <div class="relative">

                <div class="flex items-center justify-between">

                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-100 text-cyan-600">

                        <i class="fas fa-wallet text-xl"></i>
                    </div>

                    <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700">

                        {{ $lastMonthPayouts->count() }}
                        Records
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-gray-500">
                    Last Month Payout
                </p>

                <h2 class="mt-1 text-2xl font-extrabold text-gray-900">

                    ₹{{ number_format($totalLastMonthPayout, 2) }}
                </h2>
            </div>
        </article>
    </section>

    {{-- Attendance and Offices --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Attendance Overview --}}
        <div class="dashboard-card p-5 sm:p-6 xl:col-span-2">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-lg font-extrabold text-gray-900">
                        Today’s Attendance Overview
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Selected offices की live workforce availability।
                    </p>
                </div>

                <span class="inline-flex w-max items-center gap-2 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600">

                    <span class="h-2 w-2 animate-pulse rounded-full bg-green-500"></span>

                    Live Updated
                </span>
            </div>

            <div class="mt-7 grid gap-8 lg:grid-cols-3 lg:items-center">

                <div class="flex justify-center">

                    <div class="attendance-circle flex h-44 w-44 items-center justify-center rounded-full"
                        style="--attendance-percent: {{ min($attendancePercentage, 100) }}%;">

                        <div class="flex h-36 w-36 flex-col items-center justify-center rounded-full bg-white shadow-inner">

                            <span class="text-4xl font-extrabold text-gray-900">
                                {{ $attendancePercentage }}%
                            </span>

                            <span class="mt-1 text-xs font-semibold text-gray-500">
                                Attendance
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 lg:col-span-2">

                    {{-- Present Bar --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between text-sm">

                            <span class="font-semibold text-gray-700">
                                Present
                            </span>

                            <span class="font-bold text-green-600">
                                {{ $presentEmployees }}
                            </span>
                        </div>

                        <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">

                            <div class="h-full rounded-full bg-green-500"
                                style="width: {{ min($presentPercentage, 100) }}%">
                            </div>
                        </div>
                    </div>

                    {{-- Leave Bar --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between text-sm">

                            <span class="font-semibold text-gray-700">
                                On Leave
                            </span>

                            <span class="font-bold text-yellow-600">
                                {{ $onLeaveEmployees }}
                            </span>
                        </div>

                        <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">

                            <div class="h-full rounded-full bg-yellow-500"
                                style="width: {{ min($leavePercentage, 100) }}%">
                            </div>
                        </div>
                    </div>

                    {{-- Absent Bar --}}
                    <div>

                        <div class="mb-2 flex items-center justify-between text-sm">

                            <span class="font-semibold text-gray-700">
                                Absent
                            </span>

                            <span class="font-bold text-red-600">
                                {{ $absentEmployees }}
                            </span>
                        </div>

                        <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">

                            <div class="h-full rounded-full bg-red-500"
                                style="width: {{ min($absentPercentage, 100) }}%">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 pt-2">

                        <div class="rounded-xl bg-blue-50 p-3 text-center">

                            <p class="text-xl font-extrabold text-blue-700">
                                {{ $currentlyWorkingEmployees }}
                            </p>

                            <p class="mt-1 text-xs font-semibold text-blue-600">
                                Working
                            </p>
                        </div>

                        <div class="rounded-xl bg-gray-100 p-3 text-center">

                            <p class="text-xl font-extrabold text-gray-700">
                                {{ $checkedOutEmployees }}
                            </p>

                            <p class="mt-1 text-xs font-semibold text-gray-500">
                                Checked Out
                            </p>
                        </div>

                        <div class="rounded-xl bg-orange-50 p-3 text-center">

                            <p class="text-xl font-extrabold text-orange-700">
                                {{ $lateEmployees }}
                            </p>

                            <p class="mt-1 text-xs font-semibold text-orange-600">
                                Late
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Office Overview --}}
        <div class="dashboard-card p-5 sm:p-6">

            <div class="flex items-center justify-between">

                <div>
                    <h2 class="text-lg font-extrabold text-gray-900">
                        Office Overview
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Accessible office locations
                    </p>
                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">

                    <i class="fas fa-building"></i>
                </div>
            </div>

            <div class="dashboard-scroll mt-5 max-h-80 space-y-3 overflow-y-auto pr-1">

                @forelse($offices as $office)

                    @php
                        $officeEmployeeCount = $employees
                            ->where('office_id', $office->id)
                            ->count();

                        $officePresentCount = $todayCheckIn
                            ->filter(function ($attendance) use ($office) {
                                return optional(
                                    $attendance->user
                                )->office_id == $office->id;
                            })
                            ->count();

                        $isActiveOffice =
                            (int) $activeOfficeId ===
                            (int) $office->id;
                    @endphp

                    <div class="rounded-xl border p-4 transition hover:border-indigo-300 hover:bg-indigo-50
                        {{ $isActiveOffice
                            ? 'border-indigo-300 bg-indigo-50'
                            : 'border-gray-200'
                        }}">

                        <div class="flex items-center justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex items-center gap-2">

                                    <h3 class="truncate text-sm font-bold text-gray-800">
                                        {{ $office->name }}
                                    </h3>

                                    @if($isActiveOffice)
                                        <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-bold text-indigo-700">
                                            Active
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-1 text-xs text-gray-500">

                                    {{ $officeEmployeeCount }}
                                    Employees
                                </p>
                            </div>

                            <div class="text-right">

                                <p class="text-lg font-extrabold text-green-600">
                                    {{ $officePresentCount }}
                                </p>

                                <p class="text-xs uppercase tracking-wide text-gray-400">
                                    Present
                                </p>
                            </div>
                        </div>
                    </div>

                @empty

                    <div class="rounded-xl border border-dashed border-gray-300 px-4 py-10 text-center">

                        <i class="fas fa-building text-2xl text-gray-300"></i>

                        <p class="mt-3 text-sm font-semibold text-gray-600">
                            No active office found
                        </p>
                    </div>

                @endforelse
            </div>
        </div>
    </section>

    {{-- Employee Table --}}
    <section class="dashboard-card overflow-hidden">

        <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">

            <div>
                <h2 class="text-lg font-extrabold text-gray-900">
                    Employee Attendance Status
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    सभी active employees की आज की status।
                </p>
            </div>

            <div class="relative w-full sm:w-80">

                <i class="fas fa-search pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>

                <input type="text"
                    id="employee-search"
                    placeholder="Search name, email or office..."
                    class="w-full rounded-xl border border-gray-200 bg-gray-50 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            </div>
        </div>

        <div class="dashboard-scroll overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>
                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500 sm:px-6">
                            Employee
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                            Office
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                            Check In
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                            Check Out
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-500">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody id="employee-table-body"
                    class="divide-y divide-gray-100 bg-white">

                    @forelse($employees as $employee)

                        @php
                            $attendance = $attendanceMap
                                ->get($employee->id);

                            $isOnLeave = $leaveUserIds
                                ->contains($employee->id);

                            $statusText = 'Absent';
                            $statusClasses = 'bg-red-50 text-red-700';
                            $statusDot = 'bg-red-500';

                            if ($isOnLeave) {
                                $statusText = 'On Leave';
                                $statusClasses = 'bg-yellow-50 text-yellow-700';
                                $statusDot = 'bg-yellow-500';
                            } elseif (
                                $attendance &&
                                !empty($attendance->check_out)
                            ) {
                                $statusText = 'Checked Out';
                                $statusClasses = 'bg-gray-100 text-gray-700';
                                $statusDot = 'bg-gray-500';
                            } elseif ($attendance) {
                                $statusText = 'Working';
                                $statusClasses = 'bg-green-50 text-green-700';
                                $statusDot = 'bg-green-500';
                            }

                            $initials = collect(
                                explode(' ', $employee->name)
                            )
                                ->filter()
                                ->take(2)
                                ->map(function ($part) {
                                    return strtoupper(
                                        substr($part, 0, 1)
                                    );
                                })
                                ->implode('');
                        @endphp

                        <tr class="employee-row transition hover:bg-gray-50"
                            data-search="{{ strtolower(
                                $employee->name . ' ' .
                                ($employee->email ?? '') . ' ' .
                                (optional($employee->office)->name ?? '')
                            ) }}">

                            <td class="whitespace-nowrap px-5 py-4 sm:px-6">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-xs font-extrabold text-white shadow-md">

                                        {{ $initials ?: 'U' }}
                                    </div>

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-bold text-gray-900">
                                            {{ $employee->name }}
                                        </p>

                                        <p class="truncate text-xs text-gray-500">

                                            {{ $employee->email
                                                ?? $employee->phone
                                                ?? 'No contact details'
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-gray-600">

                                {{ optional($employee->office)->name
                                    ?? 'Not Assigned'
                                }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-bold text-gray-700">

                                {{ $attendance?->check_in
                                    ? Carbon::parse(
                                        $attendance->check_in
                                    )->format('h:i A')
                                    : '--:--'
                                }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4 text-sm font-bold text-gray-700">

                                {{ $attendance?->check_out
                                    ? Carbon::parse(
                                        $attendance->check_out
                                    )->format('h:i A')
                                    : '--:--'
                                }}
                            </td>

                            <td class="whitespace-nowrap px-5 py-4">

                                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold {{ $statusClasses }}">

                                    <span class="h-2 w-2 rounded-full {{ $statusDot }}"></span>

                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="5"
                                class="px-5 py-16 text-center">

                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">

                                    <i class="fas fa-users text-xl"></i>
                                </div>

                                <p class="mt-4 text-sm font-bold text-gray-700">
                                    No employees found
                                </p>

                                <p class="mt-1 text-xs text-gray-500">
                                    Selected office में कोई active employee नहीं है।
                                </p>
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="employee-no-result"
            class="hidden border-t border-gray-200 px-5 py-14 text-center">

            <i class="fas fa-search text-2xl text-gray-300"></i>

            <p class="mt-3 text-sm font-bold text-gray-700">
                No matching employee found
            </p>

            <p class="mt-1 text-xs text-gray-500">
                किसी दूसरे नाम, email या office से search करें।
            </p>
        </div>
    </section>

    {{-- Leaves and Salaries --}}
    <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        {{-- Leaves --}}
        <div class="dashboard-card overflow-hidden">

            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-5 sm:px-6">

                <div>
                    <h2 class="text-lg font-extrabold text-gray-900">
                        Today’s Leaves
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        आज leave पर रहने वाले employees।
                    </p>
                </div>

                <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-bold text-yellow-700">

                    {{ $leaves->count() }}
                </span>
            </div>

            <div class="dashboard-scroll max-h-96 divide-y divide-gray-100 overflow-y-auto">

                @forelse($leaves as $leave)

                    <div class="flex items-start justify-between gap-4 px-5 py-4 sm:px-6">

                        <div class="flex min-w-0 items-start gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-yellow-100 text-yellow-700">

                                <i class="fas fa-calendar-minus"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="truncate text-sm font-bold text-gray-900">

                                    {{ optional($leave->user)->name
                                        ?? 'Unknown Employee'
                                    }}
                                </p>

                                <p class="mt-1 line-clamp-2 text-xs text-gray-500">

                                    {{ $leave->reason
                                        ?? $leave->leave_type
                                        ?? 'Approved leave'
                                    }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">

                            <p class="text-xs font-bold text-gray-700">

                                {{ Carbon::parse(
                                    $leave->start_date
                                )->format('d M') }}

                                -

                                {{ Carbon::parse(
                                    $leave->end_date
                                )->format('d M') }}
                            </p>

                            <span class="mt-1 inline-block text-xs font-bold uppercase text-green-600">
                                Approved
                            </span>
                        </div>
                    </div>

                @empty

                    <div class="px-5 py-14 text-center">

                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600">

                            <i class="fas fa-check"></i>
                        </div>

                        <p class="mt-3 text-sm font-bold text-gray-700">
                            Everyone is available
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            आज के लिए कोई approved leave नहीं है।
                        </p>
                    </div>

                @endforelse
            </div>
        </div>

        {{-- Salaries --}}
        <div class="dashboard-card overflow-hidden">

            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-5 sm:px-6">

                <div>
                    <h2 class="text-lg font-extrabold text-gray-900">
                        Last Month Payouts
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">

                        {{ now()->subMonth()->format('F Y') }}
                        की salary payments।
                    </p>
                </div>

                <span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700">

                    ₹{{ number_format($totalLastMonthPayout, 0) }}
                </span>
            </div>

            <div class="dashboard-scroll max-h-96 divide-y divide-gray-100 overflow-y-auto">

                @forelse($lastMonthPayouts as $salary)

                    @php
                        $salaryAmount =
                            $salary->net_salary
                            ?? $salary->paid_amount
                            ?? $salary->amount
                            ?? 0;
                    @endphp

                    <div class="flex items-center justify-between gap-4 px-5 py-4 sm:px-6">

                        <div class="flex min-w-0 items-center gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-100 font-extrabold text-cyan-700">

                                ₹
                            </div>

                            <div class="min-w-0">

                                <p class="truncate text-sm font-bold text-gray-900">

                                    {{ optional($salary->user)->name
                                        ?? 'Unknown Employee'
                                    }}
                                </p>

                                <p class="mt-1 text-xs text-gray-500">

                                    {{ optional(
                                        optional($salary->user)->office
                                    )->name ?? 'Office not available' }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0 text-right">

                            <p class="text-sm font-extrabold text-gray-900">

                                ₹{{ number_format($salaryAmount, 2) }}
                            </p>

                            <p class="mt-1 text-xs font-bold uppercase text-green-600">
                                Paid
                            </p>
                        </div>
                    </div>

                @empty

                    <div class="px-5 py-14 text-center">

                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 font-extrabold text-gray-500">

                            ₹
                        </div>

                        <p class="mt-3 text-sm font-bold text-gray-700">
                            No payout record found
                        </p>

                        <p class="mt-1 text-xs text-gray-500">
                            Last month salary data available नहीं है।
                        </p>
                    </div>

                @endforelse
            </div>
        </div>
    </section>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        /*
        |--------------------------------------------------------------------------
        | Live Clock
        |--------------------------------------------------------------------------
        */

        const liveClock = document.getElementById(
            'dashboard-live-clock'
        );

        function updateLiveClock() {
            if (!liveClock) {
                return;
            }

            liveClock.textContent = new Date().toLocaleTimeString(
                'en-IN',
                {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: true
                }
            );
        }

        updateLiveClock();

        setInterval(updateLiveClock, 1000);


        {{-- BIRTHDAY JAVASCRIPT TEMPORARILY DISABLED --}}
        @if(false)
        /*
        |--------------------------------------------------------------------------
        | Birthday Popup
        |--------------------------------------------------------------------------
        */
        const birthdayPopup = document.getElementById('birthday-popup');
        const birthdayCloseButton = document.getElementById('birthday-popup-close');
        const birthdayDoneButton = document.getElementById('birthday-popup-done');
        const birthdayCelebrateButton = document.getElementById('birthday-celebrate-button');

        function createBirthdayConfetti() {
            if (!birthdayPopup) {
                return;
            }

            const confettiColors = [
                '#facc15',
                '#fb7185',
                '#22d3ee',
                '#a78bfa',
                '#34d399',
                '#ffffff'
            ];

            for (let index = 0; index < 70; index++) {
                const confetti = document.createElement('span');
                confetti.className = 'birthday-confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.background = confettiColors[
                    Math.floor(Math.random() * confettiColors.length)
                ];
                confetti.style.animationDuration = (2.8 + Math.random() * 2.2) + 's';
                confetti.style.animationDelay = (Math.random() * 0.65) + 's';
                confetti.style.setProperty(
                    '--confetti-x',
                    ((Math.random() * 180) - 90) + 'px'
                );

                birthdayPopup.appendChild(confetti);

                window.setTimeout(function () {
                    confetti.remove();
                }, 5500);
            }
        }

        function openBirthdayPopup() {
            if (!birthdayPopup) {
                return;
            }

            birthdayPopup.classList.add('is-visible');
            birthdayPopup.setAttribute('aria-hidden', 'false');
            document.body.classList.add('birthday-popup-open');
            createBirthdayConfetti();
        }

        function closeBirthdayPopup() {
            if (!birthdayPopup) {
                return;
            }

            birthdayPopup.classList.remove('is-visible');
            birthdayPopup.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('birthday-popup-open');
        }

        if (birthdayPopup) {
            window.setTimeout(openBirthdayPopup, 450);

            birthdayCloseButton?.addEventListener('click', closeBirthdayPopup);
            birthdayDoneButton?.addEventListener('click', closeBirthdayPopup);
            birthdayCelebrateButton?.addEventListener('click', createBirthdayConfetti);

            birthdayPopup.addEventListener('click', function (event) {
                if (event.target === birthdayPopup) {
                    closeBirthdayPopup();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeBirthdayPopup();
                }
            });
        }
        @endif


        /*
        |--------------------------------------------------------------------------
        | Employee Search
        |--------------------------------------------------------------------------
        */

        const searchInput = document.getElementById(
            'employee-search'
        );

        const employeeRows = document.querySelectorAll(
            '.employee-row'
        );

        const noResult = document.getElementById(
            'employee-no-result'
        );

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value
                    .toLowerCase()
                    .trim();

                let visibleRows = 0;

                employeeRows.forEach(function (row) {
                    const searchText =
                        row.dataset.search || '';

                    const isVisible =
                        searchText.includes(keyword);

                    row.classList.toggle(
                        'hidden',
                        !isVisible
                    );

                    if (isVisible) {
                        visibleRows++;
                    }
                });

                if (noResult) {
                    noResult.classList.toggle(
                        'hidden',
                        visibleRows > 0 || keyword === ''
                    );
                }
            });
        }
    });
</script>
@endpush