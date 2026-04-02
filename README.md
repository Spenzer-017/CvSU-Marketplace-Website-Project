<!-- Developer's Notes (For Myself & Maybe My Groupmates) -->

# Website Design
Color Palette:
#005F02
#427A43
#C0B87A
#F2E3BB

# Header (header.php)
In navigation link the code <?= ($activePage ?? '') === 'home' ? 'class="active"' : '' ?> just
uses a null coalescing and ternary operators, null coalescing operator is kinda like the isset()
method so if $activePage exist and is not null use it else use the default. So in this code
if the activepage is home then set its class to active else dont put anything. Oh, and also
<?= ?> is just the shorthand echo tag. There's also the hamburger menu that is responsible for
the UI for mobile users, it works by having a toggle basically, when the menu-toggle is clicked 
and nav has no class, it adds the class="open" to the nav, else if it did have class="open" then
it removes it.

_Header-CSS_
nothing really important here besides the hamburger menu and the mobile responsiveness. It
detects the width of the device if its 768px and below it activates the hamburger menu setting
the menu-toggle display to block and making the nav flex-direction to be column so that its
easier to see in mobile and other devices.

# Footer (footer.php)
Nothing important... The script below just makes a hamburger menu toggle for mobile devices.
It works by adding a class="open" in nav if it doesn't have it, and removes it if it does
have it, then CSS does it job.

_Footer-CSS_
Not much important information here either. 4 divs for 4 sections, unordered list flex-direction
are column, h5 has bolder text-weight, and anchor tags have no decoration and has hover effects.
Theres also the copyright mention div at the very bottom that displays real-time year.

# Theme Switch
NOTE: The theme switch button only works if you include both the header and the footer because
the script for the them switch button is in the footer. It works by listening to a click and
adding the class="darkmode" to the body if its not active and its null, else if its active and
has class="darkmode" it removes it. It also communicates and saves the states to the server
local storage so that it saves the state even if you quit the webstie.

_Theme-Switch-CSS_
root variables are declared so that changing it to darkmode reflects to all other CSS properties
that has the root variables as values. At lightmode the img:last-child display is set to none, so 
that the darkmode icon is the one that is on display and when its in darkmode the icon's switches.
Also the footer is not affected by the theme because, i don't like how it looks when it changes.
There's still room for improvement on the color scheme of my themes like in hover and buttons.

<!-- Weekly Report -->

> Week #1
- Made includes like header and footer for reusability across the website
- Made sure the header and footer are responsive depending on the device
- Made a simple UI with a CvSU themed color palette (could be improve later on)
- Implemented a navbar that changes between logged in users and guests
- Implemented a Dark mode feature for me to not burn my eyes

> Week #2
- Made a incomplete front-end for main pages for my website (improve/change later on)
- Also made sure that it is responsive depending on the device
- Createda a mock database to help visualize the front-end pages (replace with a real mysql database later)
    > Home Page Front-end
    - ???
    > Dashboard Front-end
    - ???
    > Browse Page Front-end
    - ???
    > Sell Page Front-end
    - ???
    > Cart Page Front-end
    - ???
    > Profile Page Front-end
    - ???

> Week 3
- Made authentication (Login, Signup, Logout)
- Made UI for authentication pages (Login, Signup)
- Made a include dedicated for database connection/access
- Remove manual forced logins and set the $_SESSION to $user in the database
- Added protection to protected pages like (dashboard, profile, etc.) redirecting guest into login page if they aren't logged in
    > Login Page Front-end
    - ???
    > Signup Page Front-end
    - ???
