<?php 
  header("Content-type: text/css");
  include '../management/customize.php';

?>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap");

body,
html {
  margin: 0;
  padding: 0;
  height: 100%;
  align-items: center;
  justify-content: center;
  font-family: "Inter", sans-serif;
  text-rendering: optimizeSpeed;
}

p {
  color: #8e7242;
  font-weight: normal;
  text-align: center;
  font-size: 14px;
}

h1 {
  font-size: clamp(24px, 2vw, 36px);
  font-weight: bold;
  letter-spacing: 1px;
  line-height: 1.2em;
  text-align: center;
  color: #8e7242;
  margin: 0;
}

h2 {
  text-align: center;
  font-size: clamp(20px, 2vw, 28px);
  color: #8e7242;
  font-weight: bold;
  text-transform: uppercase;
  margin: 0;
}

h3 {
  text-align: center;
  font-size: clamp(20px, 2vw, 26px);
  color: #8e7242;
  font-weight: bold;
  line-height: 1.2em;
  letter-spacing: 1px;
  margin: 0;
}

h4 {
  color: #8e7242;
  text-align: center;
  font-weight: normal;
  line-height: 26px;
  font-size: larger;
}

.space-between {
  justify-content: space-between;
}

.justify-center {
  justify-content: center;
}

.gap20 {
  gap: 20px;
}

.wrapper {
  display: flex;
  flex-direction: column;
  flex-wrap: nowrap;
  align-content: center;
  align-items: center;
  max-width: 450px;
  min-width: 280px;
  padding: 40px;
  animation: fadeInAnimation ease 0.5s;
  animation-iteration-count: 1;
  animation-fill-mode: forwards;
}

@keyframes fadeInAnimation {
  0% {
    opacity: 0;
    transform: translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.title {
  text-align: center;
  font-size: clamp(20px, 5vw, 28px);
  font-weight: bold;
  color: #8e7242;
}

.header-container {
  width: 100%;
  margin-bottom: 30px;
}

.options-container,
.next {
  width: 100%;
  animation: fadeInAnimation ease 1s;
}

/* .options-container .parent-answer-btn {
  animation: fadeInAnimation ease 1s;
  animation-fill-mode: forwards;
  opacity: 0;
}

.options-container .parent-answer-btn:nth-child(1) {
  animation-delay: 0.1s;
}
.options-container .parent-answer-btn:nth-child(2) {
  animation-delay: 0.2s;
}
.options-container .parent-answer-btn:nth-child(3) {
  animation-delay: 0.3s;
}
.options-container .parent-answer-btn:nth-child(4) {
  animation-delay: 0.4s;
}
.options-container .parent-answer-btn:nth-child(5) {
  animation-delay: 0.5s;
}
.options-container .parent-answer-btn:nth-child(5) {
  animation-delay: 0.6s;
} */

.quiz {
  font-size: 70px;
}

button {
  transition: all 0.5s ease;
  padding: 10px 20px;
  width: <?php echo $w; ?>;
  font-size: clamp(14px, 2vw, 16px);
  background-image: linear-gradient(
    to right,
    #2a68dc 0%,
    #4da3ff 37%,
    #3677e4 100%
  );
  border-radius: 30px;
  border: 2px solid #8e7242;
  color: <?php echo $color; ?>;
  letter-spacing: 1px;
  margin-bottom: <?php echo $mb; ?>;
}

button:hover {
  color: #8e7242;
  background-image: linear-gradient(#f9f6f1, #f9f6f1);
  transition: all 0.5s ease;
  -webkit-transform: translateY(-3px);
  transform: translateY(-3px) !important;
}

button:active {
  background-image: linear-gradient(#f9f6f1, #f9f6f1);
}

.btn-answer.selected {
  color: #8e7242;
  background-image: linear-gradient(#f9f6f1, #f9f6f1);
}

.next {
  font-weight: bold;
  background-image: linear-gradient(
    to right,
    #f5edd8 0%,
    #f5e4b6 5%,
    #f5edd8 50%,
    #d6bf8b 100%
  );
  color: #8e7242;
}

.spacer {
  height: 50px;
  width: 100%;
}

.q-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  background-color: #fff;
  border: 2px solid #dabb7b;
  padding: 30px 20px 20px 20px;
  border-radius: 10px;
  color: #8e7242;
  position: relative;
  letter-spacing: 1em;
}

.question-mark {
  position: absolute;
  top: -30px;
}

.bonus-answer-btn.selected,
.conditional-answer-btn.selected,
.parent-answer-btn.selected {
  color: #8e7242;
  background-image: linear-gradient(#f9f6f1, #f9f6f1);
}

/* Results Page */
.no-favorable-products {
  color: #ff0000;
  font-weight: bold;
  font-size: 16px;
  text-align: center;
  margin-top: 20px;
}

.suggested-products {
  display: flex;
  overflow: scroll;
  flex-direction: column;
  -ms-overflow-style: none;
  scrollbar-width: none;
  padding-bottom: var(--mask-height);
  justify-content: flex-start;
}

.masked-overflow {
  --scrollbar-width: 8px;
  --mask-height: 32px;
  overflow-y: auto;
  height: max-content;
  --mask-image-content: linear-gradient(
    to bottom,
    transparent,
    black var(--mask-height),
    black calc(100% - var(--mask-height)),
    transparent
  );
  --mask-size-content: calc(100% - var(--scrollbar-width)) 100%;
  --mask-image-scrollbar: linear-gradient(black, black);
  --mask-size-scrollbar: var(--scrollbar-width) 100%;
  mask-image: var(--mask-image-content), var(--mask-image-scrollbar);
  mask-size: var(--mask-size-content), var(--mask-size-scrollbar);
  mask-position: 0 100%, 100% 100%;
  mask-repeat: no-repeat, no-repeat;
  max-height: 500px;
}

.suggested-products::-webkit-scrollbar {
  display: none;
}

.product-container {
  background: #fff;
  border-radius: 15px;
  border: 2px solid #8e7242 !important;
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
  width: 210px;
  margin-top: 20px;
}

.product-body {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 20px;
}

.product-body p {
  font-size: 12px;
  color: #000;
  font-weight: bold;
}

.product-container:hover {
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
}

.suggested-image {
  width: 165px;
  border-radius: 10px;
}

.view-product-button {
  font-size: 11px;
  width: 100%;
  margin-bottom: 0px;
}

.voucher {
  width: 100%;
}

.nav-buttons {
  width: -webkit-fill-available;
  gap: 10px;
  display: flex;
  flex-direction: column;
  padding: 0px 10px 0px 10px;
}

.result-title h3 {
  font-weight: bold;
  font-size: 1.5rem;
  color: #8e7242;
  margin-bottom: 10px;
}

.copy-code {
  display: none;
}

.voucher-code-container {
  display: flex;
  align-items: center;
  gap: 10px;
  justify-content: center;
  margin-bottom: 30px;
}

.fa-regular.fa-clone {
  color: #8e7242;
  font-size: 17px;
}

.fa-solid.fa-check {
  color: #8e7242;
}

/* Home Page Styles*/

.home-custom-bg {
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  width: 100%;
  height: 100%;
  position: relative;
}

.body-wrapper {
  background-size: cover;
  background-position: center center;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
}

.bg1 {
  background-image: url("./images/bg-6.jpg");
}

.bg2 {
  background-image: url("./images/bg.jpg");
}

.bg3 {
  background-image: url("./images/bg-2.jpg");
}

.bg4 {
  background-image: url("./images/bg-3.jpg");
}

.bg5 {
  background-image: url("./images/bg-4.jpg");
}

.home-content {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 450px;
  margin: auto;
  justify-content: center;
}

.home-wrapper {
  display: flex;
  flex-direction: column;
  flex-wrap: nowrap;
  align-content: center;
  justify-content: space-between;
  align-items: center;
  width: 450px;
}

.home-custom-bg .content {
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  max-width: 450px;
  margin: auto;
  height: -webkit-fill-available;
}

.home-custom-bg .wrapper {
  display: flex;
  flex-direction: column;
  flex-wrap: nowrap;
  align-content: center;
  justify-content: space-between;
  align-items: center;
}

.countries-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  width: 450px;
}

.country-card {
  width: 200px;
  padding: 20px;
  margin: 10px;
  border: 2px solid #d6b77e;
  border-radius: 7.75px;
  background: #f9f6f1;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  color: #8e7242;
  text-align: center;
  font-weight: 700;
  box-shadow: 0px 5px 10px -5px rgba(0, 0, 0, 0.5);
  transition: transform 0.2s;
}

.country-card:hover {
  transform: translate(0, -10px);
}

.country-flag {
  width: 150px;
  height: auto;
  margin-bottom: 10px;
}

@media only screen and (max-width: 768px) {
  .countries-container {
    width: 400px;
  }

  .country-card {
    width: 120px;
    height: 125px;
  }

  .country-flag {
    width: 80px;
  }

  .display-none {
    display: none;
  }
}

/* Admin Dashboard */
.ADALogo {
  width: clamp(40px, 40%, 80px);
  height: auto;
  position: absolute;
  top: clamp(1%, 3%, 5%);
  right: clamp(1%, 3%, 5%);
}

.login-title {
  margin-bottom: 20px;
}

.login-title h1 {
  color: white;
  font-size: 32px;
}

.dashboard-login {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: auto;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.login-form {
  background-color: rgba(255, 255, 255, 0.2);
  padding: 30px;
  border-radius: 5px;
  width: 50vw;
}

.container input {
  width: 100%;
  height: 48px;
  padding: 10px;
  margin-bottom: 30px;
  box-sizing: border-box;
  border-radius: 4px;
  border: none;
}

.container input:focus {
  border: none;
}

*:focus {
  outline: none;
}

.loginpage-button {
  background-color: rgb(97, 119, 196);
  color: #ffffff;
  width: 100%;
  height: 48px;
  border-radius: 4px;
  border-color: none;
  border: none;
}

.password-container {
  position: relative;
}

.password-container input {
  width: 100%;
  height: 48px;
  padding: 10px;
  margin-bottom: 30px;
  box-sizing: border-box;
  border-radius: 4px;
  border: none;
}

.password-container button {
  position: absolute;
  right: 10px;
  top: 33%;
  transform: translateY(-50%);
  background-color: transparent;
  border: none;
  cursor: pointer;
}

#eye-icon {
  color: #9ba3be;
}

/* Admin Dashboard Styles */

.page-link {
  background-color: #495057 !important;
  border-color: #343a40 !important;
}
