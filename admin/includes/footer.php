  </main>

  <!-- Mobile Menu Script -->
  <script>
    // Mobile menu toggle functionality
    (function() {
      const menuToggle = document.getElementById('mobileMenuToggle');
      const sidebar = document.getElementById('adminSidebar');
      const overlay = document.getElementById('sidebarOverlay');

      if (menuToggle && sidebar && overlay) {
        // Toggle menu
        menuToggle.addEventListener('click', function() {
          sidebar.classList.toggle('active');
          overlay.classList.toggle('active');

          // Toggle icon
          const icon = this.querySelector('i');
          if (sidebar.classList.contains('active')) {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
          } else {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
          }
        });

        // Close menu when clicking overlay
        overlay.addEventListener('click', function() {
          sidebar.classList.remove('active');
          overlay.classList.remove('active');
          const icon = menuToggle.querySelector('i');
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        });

        // Close menu when clicking a link on mobile
        const sidebarLinks = sidebar.querySelectorAll('nav a');
        sidebarLinks.forEach(link => {
          link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
              sidebar.classList.remove('active');
              overlay.classList.remove('active');
              const icon = menuToggle.querySelector('i');
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
            }
          });
        });
      }
    })();
  </script>
</body>
</html>
