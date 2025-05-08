import React from 'react';
import CookieConsent, { Cookies, getCookieConsentValue } from "react-cookie-consent";

export default function Root({children}) {
  return (
    <>
      <CookieConsent
        location="bottom"
        buttonText="I agree"
        enableDeclineButton
        setDeclineCookie={true}
        cookieName="cookie-consent"
        style={{ background: "#d1c1ff", color: "#000" }}
        buttonStyle={{
            background: "#720eec",
            color: "#fff",
          }}
      >
        We use Google Analytics to understand how visitors interact with our documentation. This helps us improve your experience.
      </CookieConsent>
      {children}
    </>
  );
}