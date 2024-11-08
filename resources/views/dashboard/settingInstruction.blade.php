@extends('dashboard.layout.root')

@section('content')
    <div class="container p-5">

        <div class="alert alert-danger alert-dismissible fade show p-4" role="alert">
            <h5 class="alert-heading text-lg">Settings Guidance!</h5>
            <p>To get the best experience, please review your location setting of your browser. Make sure to enable location.</p>
            <hr>

            <ol>
                <li>
                    <strong>Google Chrome</strong>
                    <ol>
                        <li>Open <strong>Chrome</strong> and go to the website where you want to enable location.</li>
                        <li>Click on the <strong>lock icon</strong> 🔒 next to the website's URL in the address bar.</li>
                        <li>In the dropdown, look for <strong>"Location"</strong> and select <strong>"Allow"</strong>.</li>
                        <li>If the option isn’t there, go to <strong>Settings > Site Settings > Location</strong> and ensure location services are enabled.</li>
                    </ol>
                </li>
                <li>
                    <strong>Mozilla Firefox</strong>
                    <ol>
                        <li>Open <strong>Firefox</strong> and visit the website.</li>
                        <li>Click on the <strong>lock icon</strong> 🔒 in the address bar.</li>
                        <li>Choose <strong>"Permissions"</strong>, then select <strong>"Allow"</strong> for Location.</li>
                        <li>Alternatively, go to <strong>Settings > Privacy & Security > Permissions > Location</strong> and manage site permissions.</li>
                    </ol>
                </li>
                <li>
                    <strong>Microsoft Edge</strong>
                    <ol>
                        <li>Open <strong>Edge</strong> and go to the website.</li>
                        <li>Click the <strong>lock icon</strong> 🔒 beside the website’s URL.</li>
                        <li>Find <strong>"Location"</strong> and set it to <strong>"Allow"</strong>.</li>
                        <li>You can also navigate to <strong>Settings > Cookies and site permissions > Location</strong> to manage permissions.</li>
                    </ol>
                </li>
                <li>
                    <strong>Safari (macOS)</strong>
                    <ol>
                        <li>Open <strong>Safari</strong> and go to the website.</li>
                        <li>From the top menu, click <strong>Safari > Settings for This Website</strong>.</li>
                        <li>In the pop-up menu, find <strong>Location</strong> and set it to <strong>Allow</strong>.</li>
                        <li>Alternatively, go to <strong>Safari > Preferences > Websites > Location</strong> to manage site permissions.</li>
                    </ol>
                </li>
                <li>
                    <strong>Safari (iOS)</strong>
                    <ol>
                        <li>Go to <strong>Settings</strong> on your iPhone or iPad.</li>
                        <li>Scroll down and select <strong>Safari</strong>.</li>
                        <li>Tap on <strong>Location</strong> and choose <strong>Ask</strong>, <strong>Allow</strong>, or <strong>Deny</strong> to set permissions for Safari on iOS.</li>
                    </ol>
                </li>
            </ol>

        </div>
    </div>

@endsection
