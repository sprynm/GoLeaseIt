    <div id="hdr-notice">
      <div class="c-container">
        <div class="hdr-notice-content">
          <?php
            echo $this->Settings->show('HeaderNotice.text');

            if ($this->Settings->show('HeaderNotice.link') != "") {
              ?> <a href="<?php echo $this->Settings->show('HeaderNotice.link'); ?>" class="notice-btn"><?php echo $this->Settings->show('HeaderNotice.link_text'); ?></a><?php
            }
          ?>
        </div>
      </div>
    </div>
