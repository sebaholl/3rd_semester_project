<?php
/* Template Name: Survey (Code) */
get_header();

$thanks = (isset($_GET['survey']) && $_GET['survey'] === 'thanks');
$__ = function($s){ return function_exists('pll__') ? pll__($s) : __($s,'omniora'); };
$current_url = get_permalink(); // always post back to this page
?>
<section class="section">
  <div class="container survey-form">
    <header class="section-head">
      <h1 class="h2"><?php echo esc_html( $__('Tell us about your sport') ); ?></h1>
      <?php if ($thanks): ?>
        <p class="notice success"><?php echo esc_html( $__('Thanks! Your answers were saved.') ); ?></p>
      <?php endif; ?>
    </header>

    <form method="post" action="<?php echo esc_url( $current_url ); ?>">
      <?php wp_nonce_field('omniora_survey', 'omniora_survey_nonce'); ?>
      <!-- Honeypot -->
      <input type="text" name="website" value="" style="display:none" tabindex="-1" autocomplete="off" aria-hidden="true">

      <!-- Primary sport (required) -->
      <div class="form-row">
        <label for="sport"><?php echo esc_html( $__('Primary sport') ); ?> *</label>
        <select id="sport" name="sport" required>
          <option value=""><?php echo esc_html( $__('Select…') ); ?></option>
          <option value="running"><?php echo esc_html( $__('Running') ); ?></option>
          <option value="trail"><?php echo esc_html( $__('Trail Running') ); ?></option>
          <option value="hiking"><?php echo esc_html( $__('Hiking') ); ?></option>
          <option value="gym"><?php echo esc_html( $__('Gym/Training') ); ?></option>
          <option value="cycling"><?php echo esc_html( $__('Cycling') ); ?></option>
          <option value="football"><?php echo esc_html( $__('Football') ); ?></option>
        </select>
      </div>

      <!-- Skill level (optional) -->
      <div class="form-row">
        <label for="level"><?php echo esc_html( $__('Skill level') ); ?></label>
        <select id="level" name="level">
          <option value=""><?php echo esc_html( $__('Select…') ); ?></option>
          <option value="beginner"><?php echo esc_html( $__('Beginner') ); ?></option>
          <option value="intermediate"><?php echo esc_html( $__('Intermediate') ); ?></option>
          <option value="advanced"><?php echo esc_html( $__('Advanced') ); ?></option>
          <option value="pro"><?php echo esc_html( $__('Pro') ); ?></option>
        </select>
      </div>

      <!-- Terrain (required) -->
      <fieldset class="form-row" aria-describedby="terrain-help">
        <legend><?php echo esc_html( $__('Terrain you use most') ); ?> *</legend>
        <p id="terrain-help" class="muted" style="margin:.25rem 0;"><?php echo esc_html( $__('Select at least one.') ); ?></p>
        <label><input type="checkbox" name="terrain[]" value="road" required> <?php echo esc_html( $__('Road') ); ?></label>
        <label><input type="checkbox" name="terrain[]" value="track"> <?php echo esc_html( $__('Track') ); ?></label>
        <label><input type="checkbox" name="terrain[]" value="park"> <?php echo esc_html( $__('Park/Gravel') ); ?></label>
        <label><input type="checkbox" name="terrain[]" value="trail"> <?php echo esc_html( $__('Trail (mud/roots)') ); ?></label>
        <label><input type="checkbox" name="terrain[]" value="mixed"> <?php echo esc_html( $__('Mixed') ); ?></label>
      </fieldset>

      <!-- Foot width (required) -->
      <fieldset class="form-row">
        <legend><?php echo esc_html( $__('Foot width') ); ?> *</legend>
        <label><input type="radio" name="width" value="narrow"> <?php echo esc_html( $__('Narrow') ); ?></label>
        <label><input type="radio" name="width" value="standard" checked> <?php echo esc_html( $__('Standard') ); ?></label>
        <label><input type="radio" name="width" value="wide"> <?php echo esc_html( $__('Wide') ); ?></label>
      </fieldset>

      <!-- Budget (required) -->
      <fieldset class="form-row">
        <legend><?php echo esc_html( $__('Budget') ); ?> *</legend>
        <label><input type="radio" name="budget" value="under-1000"> <?php echo esc_html( $__('Under 1,000') ); ?></label>
        <label><input type="radio" name="budget" value="1000-2000" checked> <?php echo esc_html( $__('1,000–2,000') ); ?></label>
        <label><input type="radio" name="budget" value="2000-3500"> <?php echo esc_html( $__('2,000–3,500') ); ?></label>
        <label><input type="radio" name="budget" value="3500-plus"> <?php echo esc_html( $__('3,500+') ); ?></label>
      </fieldset>

      <!-- Features (optional) -->
      <fieldset class="form-row">
        <legend><?php echo esc_html( $__('Important features') ); ?></legend>
        <label><input type="checkbox" name="features[]" value="cushioning"> <?php echo esc_html( $__('Cushioning') ); ?></label>
        <label><input type="checkbox" name="features[]" value="stability"> <?php echo esc_html( $__('Stability') ); ?></label>
        <label><input type="checkbox" name="features[]" value="lightweight"> <?php echo esc_html( $__('Lightweight') ); ?></label>
        <label><input type="checkbox" name="features[]" value="grip"> <?php echo esc_html( $__('Grip') ); ?></label>
        <label><input type="checkbox" name="features[]" value="waterproof"> <?php echo esc_html( $__('Waterproof') ); ?></label>
      </fieldset>

      <!-- Consent + email (email shown only if consent) -->
      <div class="form-row">
        <label>
          <input id="consent" type="checkbox" name="consent" value="1">
          <?php echo esc_html( $__('I agree to be contacted about offers.') ); ?>
        </label>
      </div>

      <div id="email-row" class="form-row" style="display:none;">
        <label for="email"><?php echo esc_html( $__('Email (optional)') ); ?></label>
        <input id="email" type="email" name="email" placeholder="you@example.com" inputmode="email" autocomplete="email">
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn--dark"><?php echo esc_html( $__('Send') ); ?></button>
      </div>
    </form>
  </div>
</section>

<script>
(function(){
  // Show/hide email on consent
  var c = document.getElementById('consent');
  var row = document.getElementById('email-row');
  function toggle(){ if(row) row.style.display = c && c.checked ? 'block':'none'; }
  if(c){ c.addEventListener('change', toggle); toggle(); }

  // Require at least one "terrain" even on older browsers
  var terrains = document.querySelectorAll('input[name="terrain[]"]');
  if (terrains.length) {
    function validateGroup(){
      var any = false;
      terrains.forEach(function(t){ if(t.checked) any = true; });
      terrains.forEach(function(t){ t.required = !any; });
    }
    terrains.forEach(function(t){ t.addEventListener('change', validateGroup); });
    validateGroup();
  }
})();
</script>

<?php get_footer();

