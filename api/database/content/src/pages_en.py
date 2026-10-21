"""Authored English source for all site pages. Strings wrapped in T() are translatable.
Run build.py to merge translations and emit ../pages.json etc.
Facts come from the Flystat technical specification v3 and PRD A1; nothing beyond them."""

from helpers import T, block, page, cta, item, img

HERO_STAGES = dict(
    stage2_title=T("The sensor is already inside"),
    stage2_text=T("No assembly, no separate inserter. The applicator holds the sensor sterile until the moment you press it to your arm."),
    labels=dict(
        housing=T("Housing — 24 mm across, 4.5 mm thin, IP58"),
        internals=T("Board and battery — 15 days on one charge, Bluetooth and NFC"),
        patch=T("Adhesive patch — 28 mm, skin-friendly, holds through showers and sport"),
        cap=T("Applicator base — sterile cap you remove right before use"),
    ),
    stage4_title=T("That is all you wear"),
    stage4_text=T("A 24-millimetre disc on the back of your arm. One reading a minute, straight to your phone."),
)

# ---------------------------------------------------------------- shared pieces
STATS = block("stats", anchor="numbers", data=dict(
    title=T("The numbers that matter"),
    items=[
        dict(value="15", suffix="", label=T("days of wear"), text=T("One sensor, two weeks and a day. Then you replace it yourself in under a minute.")),
        dict(value="1", suffix=" min", label=T("between readings"), text=T("A reading every minute when glucose moves fast, every five when it is steady.")),
        dict(value="0", suffix="", label=T("finger pricks to calibrate"), text=T("Factory-calibrated. No test strips, no codes, nothing to enter.")),
        dict(value="60", suffix=" min", label=T("warm-up after applying"), text=T("Put it on, go about your hour, and the first reading appears.")),
    ]))

WAITLIST_FORM = block("form", anchor="waitlist", data=dict(
    cohort="waitlist", tone="light",
    title=T("Be the first to know when Flystat is available where you live"),
    text=T("We are preparing CE marking for the end of 2027 and building distribution region by region. Leave your details and we will write to you once the sensor is available in your country — no spam, no price promises we cannot keep yet."),
    button=T("Join the waiting list"),
    success=T("Thank you. You are on the list — we will write to you as soon as Flystat reaches your country.")))

PARTNER_CTA = block("cta", anchor="partners-cta", data=dict(
    tone="dark",
    title=T("Looking for a CGM to distribute in your region?"),
    text=T("We are signing memoranda with the first distributors in the Middle East, Europe and Africa. Early partners get exclusivity options, training and launch materials."),
    cta=cta(T("Become a distributor"), "/partners"), cta2=cta(T("How partnership works"), "/partners#process")))

APP_CTA = block("cta", anchor="app-cta", data=dict(
    tone="purple",
    title=T("The Flystat app is coming to the App Store and Google Play"),
    text=T("Readings every minute, clear graphs, alerts and reports you can hand to your doctor. Leave your email and we will tell you the day it goes live."),
    cta=cta(T("Tell me when it launches"), "/app#notify"), cta2=cta(T("See the app"), "/app")))

# ---------------------------------------------------------------- pages
PAGES = []

PAGES.append(page("", "home", T("Flystat — continuous glucose monitoring"), nav=T("Home"), seo_title=T("Flystat CGM — continuous glucose monitoring sensor for 15 days"),
    seo_desc=T("Flystat is a continuous glucose monitoring system: a 15-day sensor, readings every minute, no finger-prick calibration, and an app that keeps your numbers clear. CE marking planned for 2027."),
    blocks=[
        block("hero_dark", anchor="top", data=dict(
            scene="rotate",
            eyebrow=T("Continuous glucose monitoring"),
            title=T("A small sensor. A lot more freedom."),
            text=T("Flystat reads your glucose every minute for 15 days — while you run, sleep, travel and live. No finger pricks to calibrate, no guesswork, and an app that shows what is happening right now."),
            cta=cta(T("Join the waiting list"), "#waitlist"), cta2=cta(T("For distributors"), "/partners"), **HERO_STAGES)),
        STATS,
        block("how_it_works", anchor="how", data=dict(
            eyebrow=T("How it works"), title=T("Three parts. One habit you will not notice."),
            text=T("A sensor on the back of your arm, a phone in your pocket, and a report you can share with your doctor whenever you choose."),
            items=[
                item(T("A sensor the size of two coins"), T("The applicator places a tiny flexible filament just under the skin in one click. You feel a tap, not a prick. The sensor stays on for 15 days — through showers, swimming and sport."), icon="disc", image=img("renders/sensor.webp", T("Flystat applicator and sensor"))),
                item(T("Readings every minute in the app"), T("The sensor measures glucose in the fluid between your cells and sends it to your phone by Bluetooth. Fast changes are reported every minute, steady periods every five."), icon="smartphone", image=img("app/home.webp", T("App home screen"))),
                item(T("Reports you decide to share"), T("Export a clear report for your appointment — trends, time in range, events. Your data stays yours; you choose when a doctor sees it."), icon="file-text", image=img("app/reports.webp", T("Reports screen"))),
            ])),
        block("text_media", anchor="device-teaser", data=dict(
            eyebrow=T("The device"), title=T("Designed to be forgotten on your arm"),
            text=T("<p>The sensor is 24 mm across and 4.5 mm thin, weighs two grams and sits under a round patch. The disposable applicator comes assembled with the sensor inside, so applying it is one step: press, click, done.</p><p>Both are rated IP58 — showers, pools and sweaty workouts are fine.</p>"),
            image=img("renders/applicator-front.webp", T("Flystat applicator and sensor")), media_side="right", tone="light", chips=["Ø 24 mm", "4.5 mm", "2 g", "IP58"],
            cta=cta(T("Explore the device"), "/device"))),
        block("app_screens", anchor="app", data=dict(
            eyebrow=T("The app"), title=T("Your glucose, explained — not just plotted"),
            text=T("The Flystat app turns a stream of readings into something you can act on: a live number with a trend arrow, a day graph you understand at a glance, alerts before you go too low or too high, and events you log in one tap."),
            screens=[
                dict(title=T("Right now"), text=T("Current glucose, trend arrow and the last three hours — the screen you open twenty times a day."), image=img("app/home.webp", T("App home screen"))),
                dict(title=T("The day as a graph"), text=T("Meals, insulin and activity on the same line as your glucose, so patterns stop being a mystery."), image=img("app/graph.webp", T("Glucose graph"))),
                dict(title=T("Alerts that respect your sleep"), text=T("Low and high thresholds you set, with a quiet mode for the night and an urgent-low override."), image=img("app/alerts.webp", T("Alerts screen"))),
                dict(title=T("Reports for your doctor"), text=T("Time in range, daily patterns and an AGP-style summary, exported as a PDF in one tap."), image=img("app/reports.webp", T("Reports screen"))),
            ])),
        block("segments", anchor="for", data=dict(
            eyebrow=T("Who it is for"), title=T("Different days, same sensor"),
            items=[
                item(T("Type 1 and type 2 diabetes"), T("Fewer finger pricks, more time in range, and a clearer conversation with your doctor."), icon="activity", href="/for/diabetes", image=img("people/city.webp", T("Man with a Flystat sensor on his arm in the city"))),
                item(T("Parents and families"), T("Readings you can check at a glance when it is your child or your parent wearing the sensor."), icon="heart", href="/for/family", image=img("people/couple.webp", T("Couple at home, he is wearing a Flystat sensor"))),
                item(T("Sport and nutrition"), T("See how a long run, a late dinner or a new routine actually moves your glucose."), icon="zap", href="/for/sport", image=img("people/tennis.webp", T("Playing tennis with a Flystat sensor"))),
                item(T("Pregnancy"), T("Gentle, continuous monitoring when the targets are tighter and the stakes feel higher."), icon="sun", href="/for/pregnancy", image=img("people/pregnant.webp", T("Pregnant woman with a Flystat sensor on her arm"))),
            ])),
        block("steps", anchor="install", data=dict(
            eyebrow=T("Applying the sensor"), title=T("Six steps, about a minute"),
            text=T("The full illustrated guide with videos is in the user manual. Here is the short version."),
            items=[
                item(T("Pick a spot"), T("The back of your upper arm. Clean the skin with the alcohol wipe and let it dry."), icon="map-pin"),
                item(T("Open the applicator"), T("Peel the seal. The sensor is already inside — nothing to assemble."), icon="package-open"),
                item(T("Press to the skin"), T("Place the applicator flat against your arm and press firmly until you hear a click."), icon="mouse-pointer-click"),
                item(T("Lift it off"), T("The sensor stays on your arm. Press the patch edges for a few seconds."), icon="hand"),
                item(T("Pair with the app"), T("Open Flystat, hold the phone next to the sensor, confirm the code."), icon="bluetooth"),
                item(T("Wait an hour"), T("The sensor warms up for 60 minutes. Then the first reading appears and the 15 days begin."), icon="timer"),
            ],
            cta=cta(T("Read the full manual"), "/manual"))),
        block("text_media", anchor="company-teaser", data=dict(
            eyebrow=T("The company"), title=T("Made carefully, said honestly"),
            text=T("<p>Flystat is developed by the MLC group and produced on contract manufacturing lines under our engineering control — clean rooms, biocompatibility testing and batch traceability.</p><p>We are in the middle of the CE marking procedure, which we expect to complete by the end of 2027. Until then we do not sell the device and we do not quote prices or dates we cannot keep.</p>"),
            image=img("renders/sensor-exploded.webp", T("Sensor layers: housing, board and adhesive patch")), media_side="left", tone="warm",
            cta=cta(T("About the company"), "/company"))),
        block("news", anchor="news", data=dict(title=T("News and events"), limit=3)),
        PARTNER_CTA,
        block("faq", anchor="faq", data=dict(title=T("Questions people ask first"), categories=["device", "purchase"], limit=5)),
        WAITLIST_FORM,
    ]))

PAGES.append(page("device", "device", T("The Flystat device"), nav=T("Device"), menu_group="people", in_header=True,
    seo_title=T("Flystat sensor and applicator — specifications"),
    seo_desc=T("A 15-day glucose sensor 24 mm across, a one-click disposable applicator, Bluetooth and NFC, IP58 protection. Full technical specifications of the Flystat CGM system."),
    blocks=[
        block("hero_dark", anchor="top", data=dict(
            scene="rotate", eyebrow=T("The device"),
            title=T("One click. Fifteen days."),
            text=T("The applicator comes with the sensor already inside. Press it to the back of your arm, hear the click, and the sensor is in place for the next 15 days."),
            cta=cta(T("See the specifications"), "#specs"), cta2=cta(T("How to apply it"), "/manual#install"), **HERO_STAGES)),
        block("text_media", anchor="exploded", data=dict(
            eyebrow=T("What is inside"), title=T("Three parts you never have to think about"),
            text=T("<p><strong>The cap</strong> protects the sensor until you peel the seal.</p><p><strong>The sensor</strong> — a 24 mm disc, 4.5 mm thin, two grams — carries the electronics, a 3 V lithium cell for the full 15 days and a flexible filament 5.5 mm long that sits just under the skin.</p><p><strong>The base</strong> holds the spring mechanism that places the sensor in one controlled motion and retracts the guide needle immediately.</p>"),
            image=img("renders/exploded.webp", T("Exploded view: cap, sensor and base")), media_side="right", tone="dark", chips=["Ø 24 mm", "4.5 mm", "2 g", "IP58"])),
        block("spec_table", anchor="specs", data=dict(
            title=T("Technical specifications"),
            rows=[
                dict(label=T("Wear time"), value=T("15 days"), note=T("one sensor, replaced by the user")),
                dict(label=T("Measurement range"), value=T("2.2–22.2 mmol/L (40–400 mg/dL)"), note=""),
                dict(label=T("Measurement interval"), value=T("1 minute"), note=T("sent every minute during fast changes, every 5 minutes when stable")),
                dict(label=T("Calibration"), value=T("Factory"), note=T("no user calibration, no test strips")),
                dict(label=T("Warm-up"), value=T("60 minutes"), note=""),
                dict(label=T("Connectivity"), value=T("Bluetooth Low Energy up to 10 m, NFC"), note=T("sensor memory covers the full 15 days")),
                dict(label=T("Water protection"), value=T("IP58"), note=T("1.5 m for 30 minutes — showers, pools, sport")),
                dict(label=T("Sensor"), value=T("Ø 24 mm, 4.5 mm high, 2 g"), note=T("about the size of two stacked coins")),
                dict(label=T("Filament"), value=T("5.5 × 0.6 × 0.5 mm"), note=T("flexible, under the skin")),
                dict(label=T("Applicator"), value=T("90 × 46 mm, 61 g"), note=T("single-use, supplied assembled with the sensor")),
                dict(label=T("Operating temperature"), value=T("+10 … +45 °C"), note=""),
                dict(label=T("Storage and transport"), value=T("+2 … +30 °C, humidity 10–90 % non-condensing, 70–106 kPa"), note=""),
                dict(label=T("Power"), value=T("3 V lithium cell"), note=T("sealed, lasts the whole wear period")),
                dict(label=T("In the box"), value=T("Applicator with sensor, fixing patch Ø 49 mm, alcohol wipe, quick guide"), note=T("box 54 × 54 × 91 mm")),
            ],
            footnote=T("Specifications from the Flystat technical documentation, model flystat01, v3. Flystat is not yet CE-marked: the conformity assessment is in progress and completion is planned for the end of 2027. The device is not available for sale until then."))),
        block("comparison", anchor="compare", data=dict(
            title=T("How Flystat compares"),
            text=T("Against what most continuous glucose monitors on the market offer today. We do not publish accuracy figures until our clinical studies are complete — and we think you should ask every manufacturer for theirs."),
            rows=[
                dict(label=T("Wear time"), flystat=T("15 days"), market=T("10–14 days")),
                dict(label=T("Reading interval"), flystat=T("1 minute"), market=T("1–5 minutes")),
                dict(label=T("Calibration"), flystat=T("None — factory"), market=T("None or occasional finger pricks")),
                dict(label=T("Applicator"), flystat=T("One piece, pre-assembled"), market=T("One piece or two-part assembly")),
                dict(label=T("Water protection"), flystat=T("IP58, 1.5 m / 30 min"), market=T("IP27–IP28, 1 m / 30 min")),
                dict(label=T("Sensor size"), flystat=T("Ø 24 mm, 4.5 mm"), market=T("Ø 24–35 mm, 4–5 mm")),
            ],
            footnote=T("Market values are typical ranges from publicly available documentation of CGM systems as of 2026; they are not a claim about any specific product."))),
        block("cards", anchor="life", data=dict(
            eyebrow=T("Every day"), title=T("Built for the days you forget you are wearing it"), columns=3,
            items=[
                item(T("Showers and swimming"), T("IP58 means 30 minutes at 1.5 metres. Shower, swim, sweat — just pat the patch dry afterwards."), icon="droplets"),
                item(T("Sleep"), T("Lie on it. The sensor is 4.5 mm thin and the patch edges are rounded; most people stop noticing it on day two."), icon="moon"),
                item(T("Through clothes"), T("Bluetooth reaches up to 10 metres, so the phone can stay in a bag or on the bedside table."), icon="shirt"),
            ])),
        block("steps", anchor="install", data=dict(
            eyebrow=T("Applying the sensor"), title=T("One minute, six steps"),
            text=T("The applicator does the precise part. You do the pressing."),
            items=[
                item(T("Clean the skin"), T("Back of the upper arm, alcohol wipe, let it dry."), icon="map-pin"),
                item(T("Peel the seal"), T("The sensor is already inside the applicator."), icon="package-open"),
                item(T("Press until it clicks"), T("Flat against the arm, firm pressure, one click."), icon="mouse-pointer-click"),
                item(T("Lift the applicator"), T("The sensor stays on. Press the patch edges down."), icon="hand"),
                item(T("Pair"), T("Open the app and hold the phone next to the sensor."), icon="bluetooth"),
                item(T("Warm-up"), T("60 minutes later the first reading appears."), icon="timer"),
            ],
            cta=cta(T("Full manual"), "/manual"))),
        block("documents", anchor="downloads", data=dict(title=T("Documents"), types=["spec", "manual"])),
        block("faq", anchor="faq", data=dict(title=T("Device questions"), categories=["device"], limit=6)),
        WAITLIST_FORM,
    ]))

PAGES.append(page("app", "app", T("The Flystat app"), nav=T("App"), menu_group="people", in_header=True,
    seo_title=T("Flystat app — glucose readings every minute, alerts and reports"),
    seo_desc=T("The Flystat app shows your current glucose with a trend arrow, a readable day graph, low and high alerts, events in one tap and doctor-ready reports. Coming to the App Store and Google Play."),
    blocks=[
        block("hero_dark", anchor="top", data=dict(
            scene="sensor", eyebrow=T("The app"),
            title=T("A number you understand, not a chart you decode"),
            text=T("Flystat shows what your glucose is doing right now, where it is heading, and what you did an hour ago that explains it. Alerts arrive before trouble, not after."),
            cta=cta(T("Tell me when it launches"), "#notify"), cta2=cta(T("How it works with the sensor"), "/device"))),
        block("app_screens", anchor="screens", data=dict(
            eyebrow=T("Screens"), title=T("Five screens for one habit"),
            text=T("Everything you need is one tap from the home screen. Everything you might need is two."),
            screens=[
                dict(title=T("Home"), text=T("Current value, trend arrow, the last three hours and your range in one glance. Units in mmol/L or mg/dL — your choice."), image=img("app/home.webp", T("Home screen"))),
                dict(title=T("Day graph"), text=T("Glucose over 3, 6, 12 or 24 hours with your meals, insulin and activity on the same timeline."), image=img("app/graph.webp", T("Day graph"))),
                dict(title=T("Events"), text=T("Log a meal, a dose, exercise or how you feel in one tap; the app places it on the graph and learns your usual times."), image=img("app/events.webp", T("Events"))),
                dict(title=T("Alerts"), text=T("Low, high and rapid-change alerts with your thresholds, a quiet night mode and an urgent-low alarm that always sounds."), image=img("app/alerts.webp", T("Alerts"))),
                dict(title=T("Reports"), text=T("Time in range, average glucose, daily patterns and an AGP-style report as a PDF you can hand to your doctor."), image=img("app/reports.webp", T("Reports"))),
                dict(title=T("Assistant"), text=T("Ask in plain words — “why was I high after lunch yesterday?” — and get an answer based on your own data, with the caveats a good nurse would add."), image=img("app/assistant.webp", T("AI assistant"))),
            ])),
        block("cards", anchor="features", data=dict(
            eyebrow=T("Under the hood"), title=T("Quiet, private, dependable"), columns=3,
            items=[
                item(T("Works offline"), T("The sensor stores the full 15 days. If the phone is away, the app fills the gap when you reconnect."), icon="wifi-off"),
                item(T("iOS and Android"), T("Phones from the last five years with Bluetooth Low Energy. Smartwatch complications are planned after launch."), icon="smartphone"),
                item(T("Your data stays yours"), T("Readings are encrypted on the phone and in the cloud. You decide what to export and to whom; we never sell data."), icon="lock"),
                item(T("Units and languages"), T("mmol/L or mg/dL, nine interface languages, and glucose targets you can adjust with your clinician."), icon="languages"),
                item(T("Sharing — planned"), T("Sharing live readings with family and clinicians is on the roadmap for a later release, after certification. We will not promise a date."), icon="users"),
                item(T("Reports in two taps"), T("A clean PDF with the numbers clinicians actually ask for — ready for the appointment."), icon="file-text"),
            ])),
        block("form", anchor="notify", data=dict(
            cohort="subscribe", tone="light",
            title=T("Tell me when the app is live"),
            text=T("The app launches together with the device after certification. Leave your email and we will send one message — the day it is in the stores."),
            button=T("Notify me"), success=T("Done. One email, the day the app is live."))),
        block("faq", anchor="faq", data=dict(title=T("App questions"), categories=["app"], limit=6)),
    ]))

SEGMENTS = [
    ("diabetes", T("Living with diabetes"), T("Flystat for type 1 and type 2 diabetes"),
     T("Fewer pricks, more time in range, better conversations"),
     T("Whether you dose insulin several times a day or manage with tablets and food, seeing glucose every minute changes what you notice — the slow climb after breakfast, the dip during a walk, the night you did not know about."),
     [item(T("Morning"), T("Wake up to the night's graph instead of a single number. Dawn rise or a late low — you will see which."), icon="sunrise"),
      item(T("Meals"), T("Log the meal in one tap and watch the curve. In a week you know which breakfast works for you."), icon="utensils"),
      item(T("Insulin"), T("Trend arrows help you decide earlier and correct less. The app keeps the dose history next to the graph."), icon="syringe"),
      item(T("The appointment"), T("Hand over a two-page report with time in range and patterns. Spend the visit on decisions, not on reading a logbook."), icon="stethoscope")],
     "people/city.webp"),
    ("family", T("Parents and families"), T("Flystat for parents and families"),
     T("When the sensor is on someone you love"),
     T("A child with type 1, a parent who lives alone, a partner who forgets to check. Flystat makes the number visible to the person wearing it — and gives them something simple to show you."),
     [item(T("For children"), T("The sensor is small and the applicator clicks once. Readings every minute mean fewer surprise lows during school and sport."), icon="baby"),
      item(T("For older parents"), T("Large numbers, a trend arrow and alerts that are hard to miss. No strips to run out of, no codes to type."), icon="glasses"),
      item(T("Family sharing — later"), T("Live sharing with family members is planned for a release after certification. Until then, reports can be exported and sent in two taps."), icon="users"),
      item(T("Peace of mind at night"), T("An urgent-low alarm that always sounds, even in quiet mode."), icon="moon")],
     "people/older-woman.webp"),
    ("sport", T("Sport and nutrition"), T("Flystat for sport and nutrition"),
     T("See what your body actually does"),
     T("Athletes and people who simply want to eat better use continuous glucose data to understand fuelling, recovery and the meals that leave them flat at three o'clock."),
     [item(T("Training"), T("Watch glucose during a long run or a hard session and learn when to fuel — before the wall, not after."), icon="bike"),
      item(T("Recovery"), T("Overnight stability is one of the clearest signals of how well you recovered."), icon="bed"),
      item(T("Food"), T("Two breakfasts, two curves. The sensor settles the argument in a week."), icon="apple"),
      item(T("Built for sweat"), T("IP58 protection and a patch that stays put through showers, pools and humidity."), icon="droplets")],
     "people/run.webp"),
    ("pregnancy", T("Pregnancy"), T("Flystat during pregnancy"),
     T("Tighter targets, gentler monitoring"),
     T("Gestational diabetes and pregnancy with type 1 or type 2 mean more checks and narrower targets. A sensor that reads every minute replaces many of the finger pricks and shows the picture between them."),
     [item(T("After meals"), T("The hour after eating is where targets are tightest. See the full curve, not one point at sixty minutes."), icon="utensils"),
      item(T("Nights"), T("Lows at night are easier to catch with an alarm than with an alarm clock."), icon="moon"),
      item(T("For the clinic"), T("Weekly reports with time in range help your team adjust the plan without guesswork."), icon="stethoscope"),
      item(T("Comfort"), T("A 24 mm sensor on the back of the arm, out of the way of everything else that is changing."), icon="heart")],
     "people/pregnant.webp"),
]
for slug, nav, h1, title, lead, items, photo in SEGMENTS:
    PAGES.append(page(f"for/{slug}", "segment", h1, nav=nav, seo_title=h1,
        seo_desc=lead,
        blocks=[
            block("hero_light", anchor="top", data=dict(eyebrow=T("Who it is for"), title=title, text=lead, image=img(photo, nav),
                cta=cta(T("Join the waiting list"), "#waitlist"), cta2=cta(T("How the sensor works"), "/device"))),
            block("cards", anchor="day", data=dict(eyebrow=T("A day with Flystat"), title=T("Where it changes things"), columns=2, items=items)),
            block("text_media", anchor="app", data=dict(eyebrow=T("The app"), title=T("Alerts, events and a report for the clinic"),
                text=T("<p>Set your own low and high thresholds, log meals and doses in one tap, and export a report with time in range before every appointment.</p><p>Flystat is a medical device under conformity assessment; it does not replace advice from your doctor and treatment decisions should be confirmed with them.</p>"),
                image=img("app/graph.webp", T("Glucose graph in the app")), media_side="right", tone="light", cta=cta(T("See the app"), "/app"))),
            block("faq", anchor="faq", data=dict(title=T("Common questions"), categories=["device", "app", "purchase"], limit=4)),
            WAITLIST_FORM,
        ]))

PAGES.append(page("clinicians", "clinicians", T("Flystat for clinicians"), nav=T("Clinicians"), menu_group="partners", in_header=True,
    seo_title=T("Flystat CGM for clinicians — data, reports and clinical status"),
    seo_desc=T("What clinicians need to know about the Flystat continuous glucose monitoring system: measurement principle, data and reports, regulatory status and study plans."),
    blocks=[
        block("hero_light", anchor="top", data=dict(eyebrow=T("For clinicians"), title=T("Continuous data your patients will actually bring to the visit"),
            text=T("Flystat is an interstitial glucose sensor with factory calibration, a one-minute measurement cycle and a 15-day wear period. This page sets out what we can show today and what is still in progress — honestly."),
            image=img("app/reports.webp", T("Report screen")), cta=cta(T("Ask a question"), "#question"), cta2=cta(T("Technical specifications"), "/device#specs"),
            stats=[dict(value="15 d", label=T("wear period")), dict(value="1 min", label=T("measurement cycle")), dict(value="0", label=T("user calibrations"))])),
        block("text_media", anchor="method", data=dict(eyebrow=T("Measurement"), title=T("How the sensor measures"),
            text=T("<p>A flexible enzymatic filament 5.5 mm long measures glucose in the interstitial fluid. Readings are processed on the sensor, stored for the full wear period and transmitted by Bluetooth Low Energy; NFC is used for activation.</p><p>Calibration is performed at the factory; the system has no user calibration. The measurement range is 2.2–22.2 mmol/L (40–400 mg/dL) with a 60-minute warm-up.</p><p>We will publish accuracy data (MARD and consensus error grid) when our clinical studies are complete. We do not quote laboratory figures as clinical performance.</p>"),
            image=img("renders/sensor.webp", T("Sensor top view")), media_side="left", tone="light")),
        block("cards", anchor="reports", data=dict(eyebrow=T("Data and reports"), title=T("What arrives on your desk"), columns=3,
            items=[
                item(T("AGP-style report"), T("Ambulatory glucose profile over 14 days, time in range by target, glucose management indicator, variability."), icon="bar-chart-3"),
                item(T("Daily patterns"), T("Overlay of days with meals, insulin and activity events logged by the patient."), icon="calendar"),
                item(T("Export"), T("PDF from the app today; a clinic web view with patient consent is planned for a later release."), icon="file-down"),
            ])),
        block("timeline", anchor="status", data=dict(title=T("Regulatory and clinical status"),
            items=[
                dict(date="2025", title=T("Development and verification"), text=T("Sensor design frozen, biocompatibility testing of patient-contact materials, production process validation on contract lines.")),
                dict(date="2026", title=T("CE conformity assessment started"), text=T("Technical documentation under MDR; clinical evaluation plan agreed.")),
                dict(date="2027", title=T("Clinical performance studies"), text=T("Accuracy studies against reference methods; results will be published on this page.")),
                dict(date=T("end of 2027"), title=T("CE marking — planned"), text=T("Market availability follows certification, region by region through our distributors.")),
            ])),
        block("form", anchor="question", data=dict(cohort="support", tone="light", title=T("A question from a clinic or research team?"),
            text=T("Write to us — about the measurement method, data formats, study participation or pilot programmes after certification. We answer within two working days."),
            button=T("Send"), success=T("Thank you. We will reply within two working days."))),
        block("faq", anchor="faq", data=dict(title=T("Clinical questions"), categories=["clinicians"], limit=6)),
    ]))

PAGES.append(page("partners", "partners", T("Become a Flystat distributor"), nav=T("Distributors"), menu_group="partners", in_header=True, in_footer=True,
    seo_title=T("CGM distribution partnership — become a Flystat distributor"),
    seo_desc=T("Flystat is signing memoranda of understanding with distributors in the Middle East, Europe and Africa. Exclusivity options, training, launch materials and a 15-day CGM sensor to bring to your market."),
    blocks=[
        block("hero_light", anchor="top", data=dict(eyebrow=T("For distributors"), title=T("Be the first Flystat partner in your region"),
            text=T("Continuous glucose monitoring is one of the fastest-growing categories in diabetes care, and most markets still have one or two suppliers. We are building a distributor network ahead of CE marking — and the earliest partners get the best terms."),
            image=img("renders/angle.webp", T("Flystat applicator")), cta=cta(T("Request a memorandum"), "#mou"), cta2=cta(T("How it works"), "#process"),
            stats=[dict(value="15", label=T("days per sensor")), dict(value="3", label=T("priority regions")), dict(value="2027", label=T("CE marking planned"))])),
        block("steps", anchor="process", data=dict(eyebrow=T("The process"), title=T("From a conversation to a memorandum in three steps"),
            text=T("No fees, no minimum orders before certification. A memorandum of understanding fixes the region, the intent and the timeline — and gives you the materials to start preparing the market."),
            items=[
                item(T("Tell us about your market"), T("Fill in the form below: company, countries, channels, expected volumes. We reply within two working days."), icon="message-square"),
                item(T("A call and the data room"), T("A 45-minute call with our commercial team, then access to the presentation, specifications and regulatory roadmap."), icon="video"),
                item(T("Memorandum of understanding"), T("Region, exclusivity terms, launch plan and training schedule in one document. Supply agreements follow certification."), icon="file-signature"),
            ])),
        block("stats", anchor="market", data=dict(title=T("The market in numbers"),
            items=[
                dict(value="540", suffix=" M", label=T("adults living with diabetes worldwide"), text=T("International Diabetes Federation estimate; one in ten adults.")),
                dict(value="<10", suffix=" %", label=T("of them use a CGM today"), text=T("Penetration is highest in North America and Western Europe and still single-digit in most of our priority regions.")),
                dict(value="15", suffix="", label=T("days per sensor"), text=T("Longer wear means fewer sensors per patient per year and a simpler supply chain.")),
            ])),
        block("map", anchor="map", data=dict(title=T("Where we are and where we are looking"),
            text=T("Dark markers show countries with a signed partner, light markers — regions in negotiation. Everything else is open."), show_distributors=False, contact_form=False)),
        block("cards", anchor="support", data=dict(eyebrow=T("What a partner gets"), title=T("Everything needed to launch, nothing you have to invent"), columns=3,
            items=[
                item(T("Materials"), T("Presentation, specification sheet, press kit, product renders and video, website content in nine languages."), icon="folder-open"),
                item(T("Training"), T("Product and application training for your sales and support teams, online and on site."), icon="graduation-cap"),
                item(T("Regulatory support"), T("Technical documentation for local registration after CE marking, and a single contact in our regulatory team."), icon="shield-check"),
                item(T("Marketing"), T("Co-branded campaigns, event participation (Arab Health, WHX, MEDICA) and localised content."), icon="megaphone"),
                item(T("Supply"), T("Forecast-based production planning and a shared dashboard for orders after launch."), icon="truck"),
                item(T("Exclusivity options"), T("Country or regional exclusivity for partners who commit to a launch plan."), icon="key-round"),
            ])),
        block("faq", anchor="faq", data=dict(title=T("Partner questions"), categories=["partners"], limit=6)),
        block("form", anchor="mou", data=dict(cohort="distribution", tone="dark", title=T("Request a memorandum of understanding"),
            text=T("Tell us about your company and market. We reply within two working days with a call slot and access to the data room."),
            button=T("Send the request"), success=T("Thank you. We will write to you within two working days to arrange a call."))),
    ]))

PAGES.append(page("company", "company", T("About Flystat and MLC"), nav=T("Company"), menu_group="partners", in_header=True, in_footer=True,
    seo_title=T("About Flystat — the company behind the CGM"),
    seo_desc=T("Flystat is developed by the MLC group: engineering, contract manufacturing under our control, CE marking in progress. Company details, timeline and press kit."),
    blocks=[
        block("hero_light", anchor="top", data=dict(eyebrow=T("The company"), title=T("A small team making a sensor people can rely on"),
            text=T("Flystat is a brand of the MLC group. We design the sensor, the applicator and the app, and we produce them on contract manufacturing lines under our own engineering control. We say what is finished and what is not."),
            image=img("photo/cleanroom.webp", T("Flystat applicators on a tray in the clean room")), cta=cta(T("Partner with us"), "/partners"), cta2=cta(T("Press kit"), "#documents"))),
        block("timeline", anchor="timeline", data=dict(title=T("Timeline"),
            items=[
                dict(date="2025", title=T("Company founded"), text=T("Shenzhen Mailian Testing Technology Co., Ltd. registered in May 2025 as the legal entity behind Flystat.")),
                dict(date="2025", title=T("Clean rooms and production line"), text=T("Contract manufacturing set up with clean-room assembly and batch traceability.")),
                dict(date="2025", title=T("Biocompatibility"), text=T("Testing of patient-contact materials completed.")),
                dict(date="2026", title=T("Trademark and CE process"), text=T("Flystat trademark registered in the UAE; CE conformity assessment under MDR started.")),
                dict(date="2027", title=T("Clinical studies and CE marking"), text=T("Clinical performance studies; CE marking planned for the end of the year.")),
            ])),
        block("video", anchor="production", data=dict(title=T("The sensor and applicator, up close"), video="/media/hero-device.mp4", poster=img("hero/poster.webp", T("Flystat sensor and applicator")),
            caption=T("Studio animation of the current sensor and applicator design."))),
        block("cards", anchor="values", data=dict(eyebrow=T("How we work"), title=T("Three rules we do not break"), columns=3,
            items=[
                item(T("No numbers we cannot prove"), T("Accuracy figures appear here only after clinical studies. Until then we publish the specification and nothing more."), icon="check-circle"),
                item(T("No sales before certification"), T("No prices, no pre-orders with payment, no dates we cannot keep. A waiting list is the most we ask of you."), icon="shield"),
                item(T("Your data is yours"), T("The app is built so that you decide what leaves your phone. We will never sell health data."), icon="lock"),
            ])),
        block("contacts", anchor="legal", data=dict(title=T("Legal entity"), show_company=True,
            items=[dict(label=T("Distribution"), email="distributors@flycgm.com", text=T("memoranda, partnerships, pricing after certification")),
                   dict(label=T("Press"), email="press@flycgm.com", text=T("interviews, images, facts")),
                   dict(label=T("General"), email="info@flycgm.com", text="")])),
        block("documents", anchor="documents", data=dict(title=T("Press kit and documents"), types=["presskit", "presentation", "spec"])),
        block("form", anchor="cooperation", data=dict(cohort="cooperation", tone="light", title=T("Cooperation"),
            text=T("Investment, suppliers, clinics, research, press — tell us which, and the right person will reply."),
            button=T("Send"), success=T("Thank you. The right person will reply within a few days."))),
    ]))

PAGES.append(page("news", "news", T("News and events"), nav=T("News"), menu_group="people", in_header=True, in_footer=True,
    seo_title=T("Flystat news — events, product updates, research"),
    seo_desc=T("Flystat company news: exhibitions, product updates, certification progress and research."),
    blocks=[block("hero_light", anchor="top", data=dict(eyebrow=T("News"), title=T("What is happening at Flystat"), text=T("Exhibitions, product updates, certification progress — and the occasional long read about glucose.")))]))

PAGES.append(page("support", "support", T("Support"), nav=T("Support"), menu_group="people", in_header=True, in_footer=True,
    seo_title=T("Flystat support — manual, FAQ and contact"),
    seo_desc=T("Help with the Flystat sensor and app: installation guide, frequently asked questions, documents and a support form."),
    blocks=[
        block("hero_light", anchor="top", data=dict(eyebrow=T("Support"), title=T("Help with the sensor, the app and everything around them"),
            text=T("Start with the manual and the questions below. If that does not solve it, write to us — every request gets a ticket number and a reply."),
            cta=cta(T("Open the manual"), "/manual"), cta2=cta(T("Write to support"), "#ticket"))),
        block("steps", anchor="install", data=dict(eyebrow=T("Quick start"), title=T("Applying the sensor in a minute"), text="",
            items=[
                item(T("Clean the skin"), T("Back of the upper arm, alcohol wipe, let it dry."), icon="map-pin"),
                item(T("Peel the seal"), T("The sensor is already inside the applicator."), icon="package-open"),
                item(T("Press until it clicks"), T("Flat against the arm, firm pressure, one click."), icon="mouse-pointer-click"),
                item(T("Lift the applicator"), T("The sensor stays on. Press the patch edges down."), icon="hand"),
                item(T("Pair"), T("Open the app and hold the phone next to the sensor."), icon="bluetooth"),
                item(T("Warm-up"), T("60 minutes later the first reading appears."), icon="timer"),
            ], cta=cta(T("Full instructions"), "/manual#install"))),
        block("faq", anchor="faq", data=dict(title=T("Frequently asked questions"), categories=[], limit=40, searchable=True)),
        block("documents", anchor="documents", data=dict(title=T("Documents to download"), types=["manual", "spec"])),
        block("account_link", anchor="account", data=dict(title=T("Your personal account"), text=T("Readings, reports and sensor history are also available on the web — sign in with the same account as in the app."), button=T("Open the personal account"))),
        block("form", anchor="ticket", data=dict(cohort="support", tone="light", title=T("Write to support"),
            text=T("A problem with the website, a question about the device or the app, or an idea — choose the type and describe it. You will receive a ticket number by email."),
            button=T("Send"), success=T("Thank you. Your request has been registered — the ticket number is on its way to your inbox."))),
    ]))

PAGES.append(page("manual", "manual", T("User manual"), nav=T("Manual"), in_footer=True,
    seo_title=T("Flystat user manual — applying, wearing and removing the sensor"),
    seo_desc=T("The Flystat user manual online: what is in the box, applying the sensor, pairing with the app, wearing and removing, warnings, care, disposal and specifications."),
    blocks=[
        block("hero_light", anchor="top", data=dict(eyebrow=T("User manual"), title=T("Everything in the box, explained"),
            text=T("Read the manual in your language online or download the PDF. The app links to these sections directly."),
            cta=cta(T("Applying the sensor"), "#install"), cta2=cta(T("Download PDF"), "#download"))),
        block("manual_sections", anchor="sections", data=dict(title=T("Manual"), text="")),
        block("account_link", anchor="account", data=dict(title=T("Personal account"), text=T("Your readings and reports on the web, with the same login as in the app."), button=T("Open the personal account"))),
        block("faq", anchor="faq", data=dict(title=T("Related questions"), categories=["device", "support"], limit=6)),
    ]))

PAGES.append(page("contacts", "contacts", T("Contacts"), nav=T("Contacts"), in_header=False, in_footer=True,
    seo_title=T("Contact Flystat"), seo_desc=T("Flystat contacts: email addresses by topic, legal entity address, distributor map and a contact form."),
    blocks=[
        block("hero_light", anchor="top", data=dict(eyebrow=T("Contacts"), title=T("Write to the right person"), text=T("Emails by topic, our legal address, and a map of distributors by country — with a form to reach the one nearest to you."))),
        block("contacts", anchor="emails", data=dict(title=T("Emails"), show_company=True,
            items=[dict(label=T("Distribution and partnerships"), email="distributors@flycgm.com", text=T("memoranda, regions, pricing after certification")),
                   dict(label=T("Support"), email="support@flycgm.com", text=T("device, app and website questions — or use the form on the support page")),
                   dict(label=T("Clinicians and research"), email="clinical@flycgm.com", text=T("measurement method, data, study participation")),
                   dict(label=T("Press"), email="press@flycgm.com", text=T("interviews, images, facts")),
                   dict(label=T("General"), email="info@flycgm.com", text="")])),
        block("map", anchor="map", data=dict(title=T("Distributors by country"), text=T("Choose your country to contact the local distributor. Where there is none yet, the form goes to our team."), show_distributors=True, contact_form=True)),
        block("form", anchor="cooperation", data=dict(cohort="cooperation", tone="light", title=T("Anything else"), text=T("Investment, suppliers, clinics, research, press, other — pick the topic and write."), button=T("Send"), success=T("Thank you. We will reply within a few days."))),
    ]))
