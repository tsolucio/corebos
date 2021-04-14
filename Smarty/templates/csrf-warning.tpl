<html>
	<head>
		<title>CSRF Error</title>
		<link rel="stylesheet" href="include/LD/assets/styles/salesforce-lightning-design-system.css" type="text/css">
	</head>
	<body>
		<div class="demo-only" style="height:24rem">
			<section role="alertdialog" tabindex="0" aria-labelledby="prompt-heading-id"
				aria-describedby="prompt-message-wrapper" class="slds-modal slds-fade-in-open slds-modal_prompt"
				aria-modal="true">
				<div class="slds-modal__container">
					<header class="slds-modal__header slds-theme_error slds-theme_alert-texture">
						<h2 class="slds-text-heading_medium" id="prompt-heading-id">CSRF Error</h2>
					</header>
					<div class="slds-modal__content slds-p-around_medium" id="prompt-message-wrapper">
						<p>{$csrfWarning}</p>
					</div>
					<footer class="slds-modal__footer slds-theme_default">
						<button class="slds-button slds-button_neutral" onclick="window.history.back()">{$csrfReload}</button>
					</footer>
				</div>
			</section>
			<div class="slds-backdrop slds-backdrop_open"></div>
		</div>
	</body>
</html>