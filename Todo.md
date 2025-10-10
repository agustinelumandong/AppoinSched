# To-Do Tasks

## User Experience
- [X] Change "Mother's name" field to "Mother's maiden name"
- [X] Add clear office indicators throughout the request/appointment process
- [X] Change "Reference ID" to "Transaction ID" on appointment slips
- [X] Add email verification timer (2-5 minutes) after which code expires and requires resend
- [X] Add legal documents:
  - [X] Terms and conditions
  - [X] Data privacy agreement
  - [X] User consent forms
- [X] Implement review confirmation popup after user information submission
- [X] Enhance UI with more vibrant colors (panel feedback - too bland)
- [X] Optimize calendar size to fit single screen view

## Input Validation and Formatting
- [X] Add confirmation alerts for critical actions:
  - [X] "Are you sure to proceed?"
  - [X] "Are you sure this is your payment?"
- [X] Restrict text fields for numbers to only accept numeric input
- [X] Implement smart text formatting in name fields:
  - [X] Auto-capitalize first letters
  - [X] Auto-format remaining letters to lowercase
  - [X] Capitalize after spaces

## System Functionality
- [X] Add payment disclaimer for clients about:
  - [X] Accurate payments to official accounts
  - [X] Required GCash receipt screenshot uploads
  - [X] No-refund policy for incorrect/incomplete payments
- [X] Implement automatic status progression (STAFF) side: 
  - [X] "Paid" → "In Progress" automatic transition
  - [X] Add "Ready for pickup" and "Completed" status options

1. Butngan ug timer ang email verification, 2 mins or 5 mins maybe. After that dili na magamit ang code mag resend nasad.

3. butngan ug ilhanan/indicator ug asa nga office.
	- example: mag request/appointment, pag sulod na ana ang process na pero way ilhanan ug asa nga office so dapat nay ilhanan asa nga office arun dijud mamali ang client
4. pag sa numbers nga text field dapat dili maka type ug characters. Like sa password or sa user information or sa process na
5. Add terms and conditions, and data privacy agreement, and consent

7. after mag fill out and save sa user information dapat nay pop up nga review before maka save
8. Information about mother should be maiden name example: Mother’s maiden name
9. sa payment (client side) put disclaimer, example: 
Disclaimer: All payments must be made accurately to the official account details provided by our system. Clients are required to upload a valid and clear screenshot of their GCash payment receipt as proof of transaction. Please note that we do not process refunds for incorrect, incomplete, or misdirected payments. Any errors in the amount sent or transfers made to the wrong account are solely the responsibility of the client.
10. sa appointment slip kay instead nga Reference ID change to: Transaction ID
12. Dapat nay alerts pre sa mga final movements example: are you sure to proceed? Are you sure this is your payment?

6. pagamyan jud ang calendar nga masigo rag isa ka screen, ayaw lang parihasa atung last time nga napangit haha6. pagamyan jud ang calendar nga masigo rag isa ka screen, ayaw lang parihasa atung last time nga napangit haha

2. add colors kay ni reklamo ang panels bland radaw kaau

13. Sa textfield dapat automatic na ang size, example: Sa Name automatic na nga uppercase ang first letter tapos and uban kay gagmay na tapos ug mu space capital sad ang first. 

11. Sa status katung sa staff nga side na, pag mag change ug status sa request document to 🡪 Paid and ubos pud kay automatic nga In progress.  (Mag add rakog more information after mahuman kag buhat pre)
	- Ready for pickup 
	- completed


    /* background-image: linear-gradient(to right, #236ea8, #2570b4, #2771bb, #2466ad, #1f5694, #1d538d, #1c508a, #184475, #163e6d); */
    background-image: linear-gradient(269.69deg, #C4D5FF 0.04%, #FFFFFF 100%);
