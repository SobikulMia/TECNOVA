/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        navy: {
          DEFAULT: '#1E3A8A',
          50: '#EEF2FF',
          100: '#E0E7FF',
          600: '#1E3A8A',
          700: '#172E6E',
          900: '#0F1D4A',
        },
        slateblack: '#0F172A',
        accent: {
          DEFAULT: '#14B8A6', // Vibrant Teal — primary CTA
          coral: '#FB7158',   // Secondary accent (sale badges, urgency)
        },
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
        display: ['Poppins', 'ui-sans-serif', 'system-ui'],
      },
      boxShadow: {
        card: '0 4px 24px -4px rgba(15, 23, 42, 0.08)',
        'card-hover': '0 12px 32px -6px rgba(30, 58, 138, 0.18)',
      },
      borderRadius: {
        xl2: '1.25rem',
      },
    },
  },
  plugins: [],
};
