# forum

Unmaintained project which pays tribute to Pistachio: the old forum engine which
powered the Google Forums. It tries to mimick its original styles but uses
modern CSS.

> [!CAUTION]
> **DO NOT HOST THIS PUBLICLY. This project is unmaintained and has known
> security vulnerabilities.** It was originally created in 2015 and contains
> legacy code that in no way reflects my current coding standards and best
> practices (fortunately, this lets me see that I've come a long way since 2015
> in terms of designing software with security in mind! (also in terms of just
> designing software)).

## Project status
Unmaintained. Please DO NOT run this in production or anywhere. You may run it
locally if you want to experience the good old Pistachio though :)

## Story/rationale
Many modern websites are sloooooow and bloated with unnecessary things. As a
[Google Product Expert][1], I have been able to experience this with the engine
that powered Pistachio's successor: a custom version of Google Groups. This
project was a way of building the engine that I missed so much, and to show the
world that forums which load fast are possible :)

It has been a long time since I hacked this project (it's 2024 as I'm writing
this!) so I don't remember exactly why, but at some point I left the project and
it was left in the state that it is right now.

During the Covid-19 pandemic and online university classes I decided to host a
forum using this software to help my classmates share doubts and answer them (as
a Q&A platform, which is what it was meant to be, just like the Google Forums).
Unfortunately it didn't take off... except for the fact that in 2021, a random
stranger called **iq** posted the following message in the forum:

> تقوم بعمل رائع استمر في ذالك

Which translates as (thanks Google Translate):

> You're doing a great job, keep it up

That's so heartwarming, thanks **iq**! :)

The story of this piece of software still lives on as of January 2024. Someone
contacted me on Jan 17 to report a reflected XSS vulnerability in the sign in
page (thank you so much!!!). When I went to try to fix it, I saw that in fact
the entire website didn't sanitize any user input, so it was plagued with XSSs
everywhere. 🙃 That's when I decided to write this README file.

## Did you like the story?
I had fun writing the story, even if it is a short one (but I feel that good and
short is much better than long and boring! :)). If you came all the way here,
maybe you're also up to [hire me](https://www.avm99963.com/)? ;)

[1]: https://productexperts.withgoogle.com/
