</main>
        <footer>
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
        </footer>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js"></script>
    <script src="/assets/js/main.js"></script>
    <?php if (isset($extra_js)): foreach ($extra_js as $js): ?>
    <script src="<?php echo $js; ?>"></script>
    <?php endforeach; endif; ?>
</body>
</html>