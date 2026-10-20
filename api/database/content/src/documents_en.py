from helpers import T

COMPANY = "Shenzhen Mailian Testing Technology Co., Ltd."
ADDRESS = "Room 1603, Kangtai Chuangxin Plaza, Yuehai Street, Nanshan District, Shenzhen, China"

def doc(type_, slug, title, body, version="1.0", effective="2026-11-01", sort=0, files=None):
    return dict(type=type_, slug=slug, title=T(title), body=T(body), version=version, effective_at=effective, is_published=True, sort=sort, files=files or {})

PRIVACY = f"""
<p>This policy explains what personal data {COMPANY} (“Flystat”, “we”) collects on flystat.com, why, and what rights you have. It applies to the website only; the Flystat mobile app has its own privacy notice shown at first launch.</p>
<h2>Who is responsible</h2>
<p>{COMPANY}, {ADDRESS}. Email for privacy matters: privacy@flycgm.com.</p>
<h2>What we collect and why</h2>
<ul>
<li><strong>Forms you submit</strong> — name, email, phone, company, country and the text of your message. Purpose: to answer you, to put you on the waiting list you asked to join, or to start a distribution conversation. Legal basis: your consent and, for partnership requests, steps prior to a contract.</li>
<li><strong>Support requests</strong> — the same fields plus, for website errors, the page address, browser and screen size. Purpose: to fix the problem and to give you a ticket number. Support requests create a ticket in our service desk hosted by Atlassian.</li>
<li><strong>Technical data</strong> — IP address, browser and pages visited, in server logs kept for up to 30 days for security. Purpose: keeping the site running and safe. Legal basis: legitimate interest.</li>
<li><strong>Analytics cookies</strong> — only after you accept them in the cookie banner. Purpose: understanding which pages are useful. You can withdraw consent at any time from the “Cookies” link in the footer.</li>
</ul>
<p>We do not collect health data on this website. Glucose readings exist only in the app and on devices you control.</p>
<h2>How long we keep it</h2>
<p>Form submissions: until the request is closed and for 24 months afterwards, unless you ask us to delete them earlier. Waiting-list entries: until we write to you about availability in your country, or until you unsubscribe. Server logs: 30 days.</p>
<h2>Who sees it</h2>
<p>Our team, and the processors that run the website for us: hosting (Railway), email delivery, the service desk (Atlassian) and, for messages about new requests, a private Telegram group of our managers. Distributors receive only the requests you address to them through the “contact a distributor” form. We do not sell personal data.</p>
<h2>Transfers outside your country</h2>
<p>Our company is registered in China and our processors operate in the EU and the USA. Where data leaves the European Economic Area we rely on standard contractual clauses.</p>
<h2>Your rights</h2>
<p>You can ask for access, correction, deletion, restriction, portability, and you can object to processing or withdraw consent at any time by writing to privacy@flycgm.com. If you are in the EU/EEA you may also complain to your national data protection authority.</p>
<h2>Changes</h2>
<p>We will post changes on this page with a new effective date. Material changes will be announced on the site.</p>
"""

COOKIES = """
<p>Cookies are small files a website stores in your browser. Here is exactly what flystat.com uses.</p>
<h2>Strictly necessary</h2>
<p>Language choice, glucose units (mmol/L or mg/dL), your cookie decision and a session token that protects forms from abuse. These cannot be switched off because the site does not work without them. They expire within 12 months.</p>
<h2>Analytics (optional)</h2>
<p>If you accept, we load an analytics service to count visits and see which pages and languages are used. The data is aggregated and we do not use it for advertising. You can change your decision at any time from the “Cookies” link in the footer.</p>
<h2>No advertising cookies</h2>
<p>We do not use advertising or social-network tracking cookies on this website.</p>
<h2>Third parties</h2>
<p>Embedded content from other sites (for example a video player) may set its own cookies when you play it; it loads only after your click.</p>
"""

TERMS = f"""
<p>By using flystat.com you agree to these terms. They are deliberately short.</p>
<h2>What this website is</h2>
<p>An information site about the Flystat continuous glucose monitoring system operated by {COMPANY}. Nothing on it is an offer to sell: the device is under conformity assessment and is not available for purchase until CE marking is granted.</p>
<h2>Medical information</h2>
<p>Content on this site is for general information. It does not replace advice from a qualified healthcare professional and must not be used to make treatment decisions. If a glucose reading from any device does not match how you feel, confirm it with a blood glucose meter and contact your doctor.</p>
<h2>Forms and waiting list</h2>
<p>Joining the waiting list creates no obligation on either side and involves no payment. We will contact you about availability in your country; you can unsubscribe at any time.</p>
<h2>Intellectual property</h2>
<p>Flystat is a trademark of the MLC group. Texts, images, renders and video on this site belong to {COMPANY} or are used under licence; journalists may use the press kit under its stated conditions.</p>
<h2>Liability</h2>
<p>We keep the site accurate and available but do not guarantee it is error-free or uninterrupted. To the extent permitted by law we are not liable for losses arising from reliance on information published here.</p>
<h2>Governing law</h2>
<p>These terms are governed by the laws of the People's Republic of China unless mandatory consumer protection rules of your country apply.</p>
<h2>Contact</h2>
<p>info@flycgm.com · {ADDRESS}</p>
"""

DISCLAIMER = """
<p><strong>Flystat is a medical device under conformity assessment.</strong> The CE marking procedure is in progress and expected to complete by the end of 2027. Until it is granted, the device is not available for sale and no claims about clinical performance are made.</p>
<p>Flystat measures glucose in the interstitial fluid. Readings may differ from blood glucose, especially when glucose is changing quickly. Do not use readings alone to make treatment decisions; follow your healthcare professional's advice and confirm readings with a blood glucose meter when symptoms do not match the number or when the app asks you to.</p>
<p>Specifications published on this website come from the manufacturer's technical documentation and may change before market release.</p>
"""

CONSENT = """
<p>By submitting a form on flystat.com you consent to the processing of the personal data you entered for the purpose stated next to the form — answering your request, adding you to the waiting list, or starting a distribution conversation — as described in the Privacy Policy.</p>
<p>You can withdraw this consent at any time by writing to privacy@flycgm.com; withdrawal does not affect processing that happened before it.</p>
"""

MANUAL = """
<h2 id="box">1. What is in the box</h2>
<p>An applicator with the sensor already inside, a round fixing patch (Ø 49 mm), an alcohol wipe and a quick guide. Check that the seal on the applicator is intact and the expiry date on the box has not passed. Store the box at +2 … +30 °C, away from direct sunlight.</p>
<h2 id="intended">2. Intended use and important information</h2>
<p>Flystat is a continuous glucose monitoring system for people aged 18 and over. It measures glucose in the interstitial fluid every minute for up to 15 days and shows the values in the Flystat app. It is intended to complement, not replace, blood glucose testing as advised by your healthcare professional.</p>
<p><strong>Do not</strong> use the sensor if the seal is broken, the box is damaged or the expiry date has passed. <strong>Do not</strong> apply to skin with scars, moles, tattoos, irritation or near an insulin injection site. Remove the sensor before an MRI, CT scan or diathermy. If you have a known allergy to adhesives, test the patch on a small area first.</p>
<h2 id="install">3. Applying the sensor</h2>
<ol>
<li><strong>Choose the site.</strong> The back of the upper arm. Rotate arms with each new sensor.</li>
<li><strong>Clean the skin.</strong> Wash with soap, dry, then wipe with the alcohol wipe and let it dry completely. Shave the area if necessary — the patch sticks to skin, not hair.</li>
<li><strong>Open the applicator.</strong> Peel the seal from the bottom. Do not touch the inside and do not press the applicator against anything but your skin.</li>
<li><strong>Apply.</strong> Place the applicator flat against the arm. Press firmly and steadily until you hear a click; hold for two seconds.</li>
<li><strong>Remove the applicator.</strong> Lift it straight off. The sensor stays on the arm. Press the patch edges for ten seconds.</li>
<li><strong>Add the fixing patch</strong> if you expect intense sport or swimming: centre it over the sensor and smooth it down.</li>
</ol>
<p>The guide needle retracts automatically into the applicator, which can now be discarded as household plastic waste.</p>
<h2 id="pair">4. Pairing with the app</h2>
<ol>
<li>Install the Flystat app from the App Store or Google Play and sign in or create an account.</li>
<li>Tap “New sensor”, then hold the top of your phone against the sensor for a second (NFC). The app reads the sensor code.</li>
<li>Confirm Bluetooth pairing when asked. The phone must have Bluetooth switched on.</li>
<li>Warm-up starts: 60 minutes. The app shows a timer; the first reading appears when it ends.</li>
</ol>
<h2 id="wear">5. Wearing the sensor</h2>
<p>The sensor is water-protected (IP58, 1.5 m for 30 minutes): shower, swim and exercise normally and pat the patch dry afterwards. Avoid saunas above +45 °C. Keep the phone within 10 metres for continuous readings; the sensor stores the whole wear period, so gaps fill in when you reconnect.</p>
<p>Check the patch edges daily. Readings that do not match how you feel should be confirmed with a blood glucose meter.</p>
<h2 id="readings">6. Understanding readings and alerts</h2>
<p>The home screen shows the current value, a trend arrow and the last three hours. Arrows show the direction of change over the last 15 minutes. Set low and high thresholds with your healthcare professional; the urgent-low alarm always sounds, even in quiet mode. Interstitial glucose lags blood glucose by several minutes — act on the trend, not only on the number.</p>
<h2 id="remove">7. Removing and replacing</h2>
<p>After 15 days the app tells you the sensor has ended. Peel the patch slowly from the edge, folding it back on itself; warm water helps. The filament comes out with the sensor. Apply a new sensor to the other arm and pair it as in section 4.</p>
<h2 id="care">8. Care, storage and troubleshooting</h2>
<p>Store unopened applicators at +2 … +30 °C, humidity 10–90 %, pressure 70–106 kPa. Operating temperature +10 … +45 °C. If the app shows “sensor error”, follow the on-screen steps; if the error persists, remove the sensor and contact support with the sensor code.</p>
<h2 id="dispose">9. Disposal</h2>
<p>The applicator is household plastic waste once the needle has retracted. The sensor contains a lithium cell and electronics: dispose of it with electronic or battery waste according to local regulations, not with household waste.</p>
<h2 id="specs">10. Specifications</h2>
<table><tbody>
<tr><td>Wear time</td><td>15 days</td></tr>
<tr><td>Measurement range</td><td>2.2–22.2 mmol/L (40–400 mg/dL)</td></tr>
<tr><td>Measurement interval</td><td>1 minute (transmitted every minute during fast changes, every 5 minutes when stable)</td></tr>
<tr><td>Calibration</td><td>Factory; no user calibration</td></tr>
<tr><td>Warm-up</td><td>60 minutes</td></tr>
<tr><td>Connectivity</td><td>Bluetooth Low Energy up to 10 m; NFC for activation</td></tr>
<tr><td>Water protection</td><td>IP58 (1.5 m, 30 minutes)</td></tr>
<tr><td>Sensor</td><td>Ø 24 mm × 4.5 mm, 2 g; filament 5.5 × 0.6 × 0.5 mm</td></tr>
<tr><td>Applicator</td><td>90 × 46 mm, 61 g, single-use</td></tr>
<tr><td>Power</td><td>3 V lithium cell, sealed</td></tr>
<tr><td>Operating conditions</td><td>+10 … +45 °C</td></tr>
<tr><td>Storage and transport</td><td>+2 … +30 °C, 10–90 % RH non-condensing, 70–106 kPa</td></tr>
</tbody></table>
<h2 id="symbols">11. Symbols on the packaging</h2>
<p>Manufacturer · Date of manufacture · Use by · Batch code · Serial number · Do not re-use · Do not use if package is damaged · Keep dry · Temperature limit · Consult instructions for use · Not made with natural rubber latex · Contains a lithium battery — dispose of separately.</p>
<h2 id="account">12. Personal account</h2>
<p>Your readings, reports and sensor history are also available on the web in your personal account, with the same login as in the app. Use the “Personal account” button on this page or in the site header.</p>
<p><em>Manufacturer: Shenzhen Mailian Testing Technology Co., Ltd., Room 1603, Kangtai Chuangxin Plaza, Yuehai Street, Nanshan District, Shenzhen, China. This manual is a pre-release version published for information; the certified version will replace it.</em></p>
"""

DOCUMENTS = [
    doc("legal", "privacy", "Privacy Policy", PRIVACY, sort=1),
    doc("legal", "cookies", "Cookie Policy", COOKIES, sort=2),
    doc("legal", "terms", "Terms of Use", TERMS, sort=3),
    doc("legal", "disclaimer", "Medical Device Disclaimer", DISCLAIMER, sort=4),
    doc("legal", "consent", "Consent to Personal Data Processing", CONSENT, sort=5),
    doc("manual", "user-manual", "Flystat user manual", MANUAL, version="0.9 (pre-release)", effective="2026-10-20", sort=10),
    dict(type="spec", slug="specification", title=T("Technical specification (PDF)"), body="", version="v3", effective_at="2026-10-01", is_published=True, sort=20, files={}),
    dict(type="presskit", slug="press-kit", title=T("Press kit: facts, renders, logo"), body="", version="2026-10", effective_at="2026-10-20", is_published=True, sort=30, files={}),
    dict(type="presentation", slug="distributor-presentation", title=T("Presentation for distributors (PDF)"), body="", version="2026-10", effective_at="2026-10-20", is_published=False, sort=40, files={}),
]
