# Óskalisti

Simple wishlist software for a group of friends and/or family

**Main page**  
![Main page showing a list of users' wishlists and an option to add a wish to your own list without having to view your own list, thus avoiding spoilers](README-mainpage.png)

**A user's wishlist**  
![Wishlist page of Gimli, showing he wished for a finely crafted dwarven axe. There are buttons available to edit the wish, strike it off, or adding a comment. It shows that Gandalf added the wish on 2023-12-24](README-wishlist.png)

**Your profile page**  
![The profile page, where you can configure your name, color, and interface language. It also shows the email address used for your account](README-profile.png)

Óskalisti was made to be simple, both in terms of user interface as well as in terms of system requirements (it can run on a free webhost!).

Vision-impaired users should be able to use the software without much trouble due to the no-frills layout and extended use of standard HTML components.


## Usage

Let's say you use it with your mom and dad.

- Throughout the year, your mom might mention that she'd like to visit a museum far away, or maybe your dad complains about the crappy hand mixer while baking.
- You add these things to their list.
- When mom's birthday comes around, your dad and you can look at her list.
- She probably forgot that she ever mentioned this museum to you, so gifting her the day trip is a nice surprise!


## Getting started

1. Set up a standard PHP+MariaDB webserver, or use a (free) webhost
   - `mail()` needs to be functional for sending login emails
     - Allowed email addresses are set by the administrator (you), so no risk of spam being sent
   - PHP 7.0 (or newer) is needed for the `random_bytes()` function
   - Nginx, Apache, Caddy, Lighttpd... any web server will work (`.htaccess` is **not** used)
2. Place the files in the desired directory within your document root
   - Placing it in a subdirectory works automatically, no need to deploy it to the root
   - Including the `.git` directory is not a problem because it's open source software, and makes updating easy with `git pull`
3. Copy `config-example.php` to `config.php` and fill in the database connection details (username, password, database name)
4. Open the website in your browser. This triggers the database creation script automatically
5. Create an Óskalisti account for yourself: `INSERT INTO users (email, name, admin) VALUES('you@example.org', 'Galadriel', 1);`

Done!  
You can now log in and invite other users on the administration screen.


## Permission model

- Nothing can be seen before logging in
  - Normal wishes are visible for anyone who is logged in
  - Personal wishes can be viewed by the person who made it, and the person whose list it was added to
- Everyone:
  - Has a wishlist
  - Can view anyone's list and add (personal or normal) wishes for them
  - Can delete only the wishes which they created
  - Can edit anyone's wish (unless they are marked as personal, because then you cannot see it exists)
    - It is meant for a group of friends or family, where there is trust
    - The "last edited by" field cannot be cleared, so you can always see who did something
- Administrators:
  - Can delete any wish
  - Can create accounts and see a list of current accounts
  - **Cannot** see wishes marked as personal (but the server owner can use database access to see what data is stored on their server)


## Improvements

Features that would be nice to have:

- Allowing people to configure who can see their list, such that different groups of people can use one installation of Óskalisti (so long as they all know an administrator that can invite them)
- Being able to use a password to log in, for those who prefer that. Considerations:
  - Everyone's list can be seen or modified using the weakest login
  - The current email-based login is fairly strong without relying on people remembering or otherwise managing strong and unique passwords, but power users often prefer using a password manager
- Supporting uploading more than one image per wish
- Supporting alt texts for user-uploaded images. People can work around this currently by just adding the extra information in the wish's description.
- Improve image zooming. The current system breaks the layout on some mobile browsers (while zooming). Maybe images should simply open in a new tab rather than enlarging inline?
- See a few technical `TODO`s in the code, e.g.: client-side image size checking or language header parsing

Other improvements or feature requests are also welcome!


## Trivia

- Óskalisti means wishlist in Icelandic, breaking down as óska for wish (it shares a root with the English word "ask") and listi for list
- Wenslys (Afrikaans) was the original name of the project, but whenever the developer glanced over that word in their browser, it made them think of Wendy's
- Initial development was started on 2023-11-26
- SVG image uploads are supported because SVG is awesome, but this lead to a cross-site scripting vulnerability when a user opens the uploaded SVG in a new tab or otherwise views it directly.
  An SVG can contain scripts and this is executed in the website's context when the image is rendered outside of an &lt;img&gt; tag. This was caught before the first release and a
  Content Security Policy header now denies script execution for such images. Safety advice was also added to relevant Stackoverflow pages so others hopefully don't make the same mistake.
- This software is distributed under the AGPL license, version 3, which can be viewed in the `LICENSE.txt` file
  - This license grants you the software freedoms: run the software as you want, learn from it, modify it in any way, and share it with others, under the condition that you also grant these freedoms to others

---

This repository is mirrored between [Codeberg](https://codeberg.org/lucg/oskalisti) and [GitHub](https://github.com/lgommans/oskalisti)

