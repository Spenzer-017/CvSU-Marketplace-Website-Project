<?php 
  /*
    info.php - CvSU Marketplace Info Page
    Visible to both guests and logged-in users.
  */

  session_start();
  require_once "includes/db.php";

  $user = $_SESSION['user'] ?? null; 
?>

<?php  
  $activePage = "info"; 
  include "includes/header.php"; 
?>

<!-- Page Hero -->
<section class="info-hero">
  <div class="info-hero-inner">
    <span class="info-hero-badge">CvSU Marketplace</span>
    <h1>How can we help you?</h1>
    <p>Learn about the platform, find answers to common questions, or get in touch with us.</p>
    <div class="info-nav-pills">
      <a href="#about-us-section" class="info-pill">About Us</a>
      <a href="#faq-section" class="info-pill">FAQ</a>
      <a href="#contact-section" class="info-pill">Contact</a>
    </div>
  </div>
</section>

<!-- About Us Section -->
<div class="info-section-full" id="about-us-section">
  <div class="info-inner">

    <div class="info-section-label">About Us</div>
    <h2 class="info-section-title">Built by students, for students.</h2>
    <p class="info-section-sub">
      CvSU Marketplace is a campus-exclusive buy-and-sell platform designed to make it simple, safe, and free all within the Cavite State University community.
    </p>

    <div class="about-grid">
      <div class="about-card">
        <div class="about-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px"><path d="M480-80q-139-35-229.5-159.5T160-516v-244l320-120 320 120v244q0 152-90.5 276.5T480-80Zm0-84q97-30 162-115.5T718-480H480v-315l-240 90v207q0 7 .5 13.5T242-480h238v316Z"/></svg>
        </div>
        <h3>Safe & Trusted</h3>
        <p>Only verified CvSU students can sign up. Every seller and buyer is a fellow CvSU student you can trust.</p>
      </div>

      <div class="about-card">
        <div class="about-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px"><path d="M475-160q4 0 8-2t6-4l328-328q12-12 17.5-27t5.5-30q0-16-5.5-30.5T817-607L647-777q-11-12-25.5-17.5T591-800q-15 0-30 5.5T534-777l-11 11 74 74q15 14 22 32t7 38q0 42-28.5 70.5T527-523q-20 0-38.5-7T456-552l-73-74-156 156q-6 6-8.5 13t-2.5 14q0 16 11 27.5t27 11.5q4 0 8-2t6-4l136-136 56 56-135 136q-6 6-8.5 13t-2.5 14q0 16 11 27t27 11q4 0 8-1.5t6-4.5l136-135 56 56-136 136q-6 5-8.5 12.5T421-300q0 16 11 27.5t27 11.5q5 0 9-2t7-4l135-135 56 57-133 133q-6 6-8 13t-2 15q0 16 11.5 27T475-160ZM474-80q-42 0-70-30t-28-72q-44-5-72-34.5T276-288q-44-5-71-34t-29-72q-48-9-76.5-41T71-511q0-22 8.5-43t24.5-37l285-285q11-12 25.5-17.5T444-899q15 0 30 5.5t27 17.5l74 74q5 5 10 8t11 3q10 0 17-7t7-17q0-6-3-11t-8-10l-75-74 42-42q12-12 26.5-17.5T634-975q15 0 29 6t25 18l170 169q23 23 36.5 53t13.5 62q0 31-13 61.5T859-553L531-225q-14 14-34 19.5t-23 5.5Z"/></svg>
        </div>
        <h3>Community First</h3>
        <p>Everything stays on campus. Meet up at familiar spots, deal with people you see every day.</p>
      </div>

      <div class="about-card">
        <div class="about-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px"><path d="M884-370 622-108q-14 14-30.5 21t-33.5 7q-17 0-33.5-7T494-108L76-526q-14-14-21-32t-7-38v-284q0-40 28-68t68-28h284q20 0 38 7t32 21L878-470q14 14 21 31t7 34q0 18-7 34t-15 21Zm-57-57L541-743q-6-6-13-9t-15-3H229q-17 0-28.5 11.5T189-715v284q0 8 3 15t9 13l418 418 208-208ZM348.5-648q22.5 0 38-15.5t15.5-38-15.5-38T348-755t-38 15.5-15.5 38 15.5 38 38 15.5Z"/></svg>
        </div>
        <h3>Always Free</h3>
        <p>No listing fees, no commissions, no hidden fees. Post and buy as much as you want at zero cost.</p>
      </div>

      <div class="about-card">
        <div class="about-icon">
          <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px"><path d="M440-122q-121-15-200.5-105.5T160-440q0-66 26-126t72-108l57 57q-38 38-56.5 86T240-440q0 88 56 155.5T440-202v80Zm80 0v-80q87-16 144-84t57-156q0-90-56-155t-145-80v160l-200-200 200-200v160q125 14 212.5 105.5T840-440q0 121-79.5 211T520-122Z"/></svg>
        </div>
        <h3>Sustainable Transaction</h3>
        <p>Give pre-loved items a second life. Sell old textbooks, gadgets, and supplies to those who need them.</p>
      </div>
    </div>

    <!-- Mission strip -->
    <div class="about-mission">
      <div class="about-mission-text">
        <div class="info-section-label" style="color: var(--accent); border-color: rgba(192,184,122,0.4);">Our Mission</div>
        <h3>Empowering student-to-student interaction at CvSU</h3>
        <p>
          We believe every student deserves access to affordable resources. CvSU Marketplace makes buying and selling easier by having no middlemen, no strangers, no risk so you can focus on what matters.
        </p>
      </div>
      <div class="about-mission-stat-col">
        <div class="mission-stat">
          <span class="mission-stat-value">100%</span>
          <span class="mission-stat-label">CvSU-Exclusive</span>
        </div>
        <div class="mission-stat">
          <span class="mission-stat-value">Free</span>
          <span class="mission-stat-label">Always, no fees</span>
        </div>
        <div class="mission-stat">
          <span class="mission-stat-value">Safe</span>
          <span class="mission-stat-label">Verified accounts</span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- FAQ Section -->
<div class="info-section-bg" id="faq-section">
  <div class="info-inner">

    <div class="info-section-label">FAQ</div>
    <h2 class="info-section-title">Frequently Asked Questions</h2>
    <p class="info-section-sub">
      Got questions? We've got answers. If you can't find what you're looking for, feel free to reach out below.
    </p>

    <div class="faq-grid">

      <!-- Column 1 -->
      <div class="faq-col">
        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            Who can use CvSU Marketplace?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              CvSU Marketplace is exclusively for currently enrolled Cavite State University students. Registration requires a valid CvSU student email address to ensure a safe and trusted community.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            Is it free to post a listing?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              Yes, it's completely free. There are no listing fees, no transaction fees, and no premium tiers. The platform is and will always be free for all CvSU students.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            What can I sell on the marketplace?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              You can sell books, school supplies, electronics, clothing, food, offer services, and more. Items must be legal, safe, and appropriate for a campus community.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            How do meetups and payments work?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              Buyers and sellers coordinate directly through the built-in messaging system. All transactions happen in person on campus at a meetup spot agreed upon by both parties. We recommend meeting in public, well-lit campus areas.
            </p>
          </div>
        </div>
      </div>

      <!-- Column 2 -->
      <div class="faq-col">
        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            How do I mark an item as sold?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              Once a transaction is completed, the seller can mark the transaction as complete from the Transactions page or from the chat window. This automatically updates the listing status so other buyers know it's no longer available.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            Can I edit or delete my listing?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              Yes, you can edit or delete any of your active listings at any time from your Dashboard or My Listings page. Once a transaction is created for a listing, certain edits may be restricted until the transaction is resolved.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            What should I do if a seller is unresponsive?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              If a seller is unresponsive, you can cancel the transaction from your Transactions page and move on. If you believe there is any suspicious or abusive behavior, please contact us using the form in the Contact section below.
            </p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" aria-expanded="false">
            Is my personal information safe?
            <svg class="faq-chevron" xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-345 240-585l56-56 184 184 184-184 56 56-240 240Z"/></svg>
          </button>
          <div class="faq-answer">
            <p>
              We only collect information necessary to run the platform (i.e. your name, student email, and course). Your data is never sold or shared with third parties. Messages are only visible to the two parties in a conversation.
            </p>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Contact Section -->
<div class="info-section-full" id="contact-section">
  <div class="info-inner">

    <div class="info-section-label">Contact</div>
    <h2 class="info-section-title">Get in touch with us</h2>
    <p class="info-section-sub">
      Have a concern, report an issue, or just want to say hi? Fill out the form and we'll get back to you as soon as we can.
    </p>

    <div class="contact-layout">

      <!-- Contact Form -->
      <div class="contact-form-wrap">
        <?php if (isset($_GET['sent']) && $_GET['sent'] === '1'): ?>
          <div class="alert alert-success" style="margin-bottom: 20px;">
            Your message was sent successfully! We'll get back to you soon.
          </div>
        <?php endif; ?>

        <form class="contact-form" action="contact-handler.php" method="POST">
          <div class="form-group">
            <label for="contact-name">Full Name <span class="required">*</span></label>
            <input type="text" id="contact-name" name="name" placeholder="e.g. Juan dela Cruz" required value="<?= $user ? htmlspecialchars($user['name']) : '' ?>" />
          </div>

          <div class="form-group">
            <label for="contact-email">Email Address <span class="required">*</span></label>
            <input type="email" id="contact-email" name="email" placeholder="e.g. juandelacruz@cvsu.edu.ph" required
              value="<?= $user ? htmlspecialchars($user['email']) : '' ?>"
              <?= $user ? 'readonly' : '' ?> />
            <?php if ($user): ?>
              <span class="form-hint">Using your registered CvSU email.</span>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <label for="contact-subject">Subject <span class="required">*</span></label>
            <select id="contact-subject" name="subject" required>
              <option value="" disabled selected>Select a topic</option>
              <option value="report-user">Report a User</option>
              <option value="report-listing">Report a Listing</option>
              <option value="account-issue">Account Issue</option>
              <option value="bug-report">Bug / Technical Problem</option>
              <option value="suggestion">Suggestion or Feedback</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="contact-message">Message <span class="required">*</span></label>
            <textarea id="contact-message" name="message" rows="5" placeholder="Describe your concern" required></textarea>
          </div>

          <button type="submit" class="btn-submit">Send Message</button>
        </form>
      </div>

      <!-- Contact Info Side -->
      <div class="contact-info-col">
        <div class="contact-info-card">
          <h3>Other ways to reach us</h3>

          <div class="contact-info-item">
            <div class="contact-info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h640q33 0 56.5 23.5T880-720v480q0 33-23.5 56.5T800-160H160Zm320-280L160-640v400h640v-400L480-440Zm0-80 320-200H160l320 200ZM160-640v-80 480-400Z"/></svg>
            </div>
            <div>
              <div class="contact-info-label">Email</div>
              <div class="contact-info-value">kevinspenzer.lima@cvsu.edu.ph</div>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-186q122-112 181-203.5T720-552q0-109-69.5-178.5T480-800q-101 0-170.5 69.5T240-552q0 71 59 162.5T480-186Zm0 106Q319-217 239.5-334.5T160-552q0-150 96.5-239T480-880q127 0 223.5 89T800-552q0 100-79.5 217.5T480-80Zm0-480Zm0 80q33 0 56.5-23.5T560-560q0-33-23.5-56.5T480-640q-33 0-56.5 23.5T400-560q0 33 23.5 56.5T480-480Z"/></svg>
            </div>
            <div>
              <div class="contact-info-label">Department</div>
              <div class="contact-info-value">CEIT & DIT</div>
            </div>
          </div>

          <div class="contact-info-item">
            <div class="contact-info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px"><path d="M480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Zm0-80q134 0 227-93t93-227q0-134-93-227t-227-93q-134 0-227 93t-93 227q0 134 93 227t227 93Zm0-320Zm28 216-14-90q-19-7-40-19.5T418-402l-84 28-28-48 70-54q-2-10-3-20t-1-20q0-10 1-20t3-20l-70-54 28-48 84 28q14-14 35-26.5t40-19.5l14-90h56l14 90q19 7 39.5 19.5T618-558l84-28 28 48-70 54q2 10 3 20t1 20q0 10-1 20t-3 20l70 54-28 48-84-28q-14 14-34.5 26.5T540-284l-14 90h-28Z"/></svg>
            </div>
            <div>
              <div class="contact-info-label">Support Hours</div>
              <div class="contact-info-value">Mon - Fri, 12:00 PM - 6:00 PM</div>
            </div>
          </div>
        </div>

        <div class="contact-info-card contact-response-note">
          <div class="response-icon"><?= $messageIcon ?></div>
          <p>
            We typically respond within <strong>1–3 business days</strong>. For urgent concerns, please include as much detail as possible in your message.
          </p>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
	// FAQ Accordion
	document.querySelectorAll('.faq-question').forEach(btn => {
		btn.addEventListener('click', () => {
			const answer = btn.nextElementSibling;
			const isOpen = btn.getAttribute('aria-expanded') === 'true';

			// Close all others
			document.querySelectorAll('.faq-question').forEach(other => {
				other.setAttribute('aria-expanded', 'false');
				other.nextElementSibling.classList.remove('open');
			});

			// Toggle clicked
			if (!isOpen) {
				btn.setAttribute('aria-expanded', 'true');
				answer.classList.add('open');
			}
		});
	});
</script>

<?php include 'includes/footer.php'; ?>