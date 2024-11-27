# CakeDC implementation of oauth2 for Cognito

Copied from https://github.com/CakeDC/oauth2-cognito

## Usage
Usage is the same as The League's OAuth client, using \CakeDC\OAuth2\Client\Provider\Cognito as the provider.

### Authorization Code Flow

```
$provider = new CakeDC\OAuth2\Client\Provider\Cognito([
    'clientId'          => '{cognito-client-id}',
    'clientSecret'      => '{cognito-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
    'cognitoDomain'     => '{cognito-client-domain}', 
    'region'            => '{cognito-region}' 
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getEmail());

    } catch (Exception $e) {

        // Failed to get user details
        exit(':(');
    }
}
```

### Managing Scopes

When creating your Amazon Cognito authorization URL, you can specify the state and scopes your application may authorize.

```
$options = [
    'state' => 'OPTIONAL_CUSTOM_CONFIGURED_STATE',
    'scope' => ['email','phone','profile']
];

$authorizationUrl = $provider->getAuthorizationUrl($options);

If neither are defined, the provider will utilize internal defaults.

At the time of authoring this documentation, the following scopes are available:

    phone
    email
    profile
    openid (required for phone, email or profile)
    aws.cognito.signin.user.admin
```