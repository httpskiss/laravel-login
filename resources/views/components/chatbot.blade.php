<!-- Kommunicate Chat Bot Component -->
<div id="km-chat-widget"></div>
<script>
    (function(d, m){
        var kommunicateSettings = {
            "appId": "18c9ab76ed503b34670b2caed93e3bf56", // Replace with your Kommunicate App ID
            "popupWidget": true,
            "automaticChatOpenOnNavigation": false,
            "onInit": function() {
                var kmSettings = {
                    "headerBackground": "#1e3a8a",
                    "botTitle": "BiPSU HRMIS Assistant",
                    "botAvatarUrl": "{{ asset('assets/images/one_bipsu.png') }}",
                };

                // Define custom bot responses
                var customResponses = [
                    {
                        "intent": "what_is_bipsu_hrmis",
                        "keywords": ["what is bipsu hrmis", "about hrmis", "hrmis system"],
                        "response": "BiPSU HRMIS (Human Resource Management Information System) is a comprehensive platform designed for Biliran Province State University employees. It manages employee profiles, attendance tracking, leave applications, and other HR-related functions."
                    },
                    {
                        "intent": "how_to_login",
                        "keywords": ["how to login", "login process", "sign in help"],
                        "response": "To log in to BiPSU HRMIS:\n1. Visit the login page\n2. Enter your university email address\n3. Enter your password\n4. Click 'Sign in'\n\nYou can also use Google or Microsoft account if enabled for your institution."
                    },
                    {
                        "intent": "how_to_register",
                        "keywords": ["how to register", "sign up", "create account"],
                        "response": "To register for BiPSU HRMIS:\n1. Click 'Register here' on the login page\n2. Fill in your personal details (First Name, Last Name)\n3. Enter your university email\n4. Create a strong password\n5. Enter your Employee ID\n6. Select your Department\n7. Accept Terms of Service\n8. Click 'Create Account'"
                    },
                    {
                        "intent": "how_to_edit_profile",
                        "keywords": ["edit profile", "update profile", "change details"],
                        "response": "To edit your profile:\n1. Log in to your account\n2. Click on your profile picture/name in the top right\n3. Select 'Edit Profile'\n4. Update your information in the Personal Data Sheet (PDS)\n5. Save your changes\n\nMake sure to keep your information up to date!"
                    },
                    {
                        "intent": "how_to_apply_leave",
                        "keywords": ["apply leave", "leave application", "request time off"],
                        "response": "To apply for leave:\n1. Log in to your account\n2. Go to 'Leave Management'\n3. Click 'New Leave Application'\n4. Select leave type (Vacation, Sick, etc.)\n5. Enter dates and reason\n6. Attach supporting documents if required\n7. Submit for approval\n\nYou can track your application status in the Leave Dashboard."
                    }
                ];

                // Register custom responses with Kommunicate
                Kommunicate.registerCustomEventHandler('onMessageReceived', function(message) {
                    if (message.type === 'message' && message.source === 1) { // Message from user
                        var userMessage = message.message.toLowerCase();
                        
                        customResponses.forEach(function(item) {
                            if (item.keywords.some(keyword => userMessage.includes(keyword))) {
                                Kommunicate.sendMessage({
                                    message: item.response,
                                    type: 'text'
                                });
                            }
                        });
                    }
                });

                Kommunicate.updateSettings(kmSettings);
            }
        };
        var s = document.createElement("script"); 
        s.type = "text/javascript"; 
        s.async = true;
        s.src = "https://widget.kommunicate.io/v2/kommunicate.app";
        var h = document.getElementsByTagName("head")[0]; 
        h.appendChild(s);
        window.kommunicate = m; 
        m._globals = kommunicateSettings;
    })(document, window.kommunicate || {});
</script>