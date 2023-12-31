/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {
      colors: {
        white: "#ffffff",
        primary: {
          200: "#EFEFFC",
          300: "#CFD0F6",
          400: "#7F81E8",
          DEFAULT: "#5F62E2",
          600: "#5658CB",
        },
        secondary: {
          200: "#EAF8F4",
          300: "#BFE9DE",
          400: "#56C4A7",
          DEFAULT: "#2AB691",
          600: "#26A482",
        },
        gray: {
          300: "#FAFAFA",
          400: "#F2F2F2",
          500: "#E5E5E5",
          600: "#B2B2B2",
          700: "#808080",
          800: "#333333",
          DEFAULT: "#1D1D1D",
        },
        alert: {
          danger:"#FF4E4E",
          warning:"#90DA1",
          success:"#FEB72F",
        }
      },
      fontSize: {
        "8xl":[
          "120px",
          {
            lineHeight: "120px",
            letterSpacing: "-6px",
            fontWeight: "500",
          },
        ],
        "7xl": [
          "72px",
          {
            lineHeight: "80px",
            letterSpacing: "-4.5px",
            fontWeight: "600",
          },
        ],
        "6xl": [
          "55px",
          {
            lineHeight: "60px",
            letterSpacing: "-2.5px",
            fontWeight: "500",
          },
        ],
        "5xl": [
          "48px",
          {
            lineHeight: "54px",
            letterSpacing: "-1.600000023841858px",
            fontWeight: "500",
          },
        ],
        "4xl": [
          "36px",
          {
            lineHeight: "54px",
            letterSpacing: "-1.2000000476837158px",
            fontWeight: "500",
          },
        ],
        "3xl": [
          "28px",
          {
            lineHeight: "44px",
            letterSpacing: "-0.800000011920929px",
            fontWeight: "500",
          },
        ],
        "2xl": [
          "24px",
          {
            lineHeight: "30px",
            letterSpacing: "-1px",
            fontWeight: "400",
          },
        ],
        xl: [
          "24px",
          {
            lineHeight: "30px",
            letterSpacing: "-1px",
            fontWeight: "400",
          },
        ],
        lg: [
          "21px",
          {
            lineHeight: "30px",
            letterSpacing: "-0.800000011920929px",
            fontWeight: "400",
          }
        ],

        base: [
          "17px",
          {
            lineHeight: "25px",
            letterSpacing: "-0.699999988079071px",
            fontWeight: "400",
          
          }
        ],
        sm: [
          "15px",
          {
            lineHeight: "23px",
            letterSpacing: "-0.6000000238418579px",
            fontWeight: "400",
          }
        ],
        caption1: [
          "20px",
          {
            lineHeight: "24px",
            letterSpacing: "-0.6000000238418579px",
            fontWeight: "400",
          }
        ],
        caption2: [
          "18px",
          {
            lineHeight: "20px",
            letterSpacing: "-0.30000001192092896px",
            fontWeight: "400",
          }
        ],
        caption3: [
          "16px",
          {
            lineHeight: "18px",
            letterSpacing: "-0.5px",
            fontWeight: "400",
          }
        ],
        caption4: [
          "13px",
          {
            lineHeight:  "15px",
            letterSpacing: "-0.20000000298023224px",
            fontWeight: "400",
          }
        ],
      },
      borderRadius: {
        DEFAULT: "10px",
        full: "9999px",
      },
    },
  },
  plugins: [require("@tailwindcss/forms")],
}

