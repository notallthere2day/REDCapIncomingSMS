# REDCapIncomingSMS
Some custom code for handling incoming Twilio SMS messages in a REDCap project

Here is some code I wrote for handling incoming text messages from study participants.
Often, when you are using Twilio ot send alerts and survey invites from REDCap, participants respond by replying to the text message. Standard behaviour in REDCap (unless you are doing an SMS conversation survey) is for these incoming text messages to disappear into the void.

This code will save incoming text messages to a repeating form in your project, where you can read them, mark them as read, and even reply to them directly from REDCap.
It will check the 'from number' of the incoming text against the participant phone numbers in the project, and if it finds a match, it will store the SMS in the appropriate record. If it does not find a matchm it will store it in record #1 by default.

# Files:

## SMS_2022-01-11_1233.zip: 
A zip file of the instrument used to save and send SMS messages. This should be added to your project and set as a repeating form. 
![image](https://user-images.githubusercontent.com/5281692/149120192-e4e80bb2-a359-4a03-84ad-8341c5368f85.png)

## home_page.php: 
A hook file to display your 'SMS inbox' on your project home page. This shows all your unred incoming messages, with links through to the record home page. It will also display your remaining Twilio Account balance.

![image](https://user-images.githubusercontent.com/5281692/149119988-f72877aa-cbb6-44db-b838-551b48219014.png)

## twiliohook.php:
This is the file that does most of the work parsing and saving incoming text messages. You'll need to put it in the hooks folder for your redcap project, and also set it as the incoming SMS webhook in your Twilio messaging service. **Nb. this means that you won't be able to use SMS conversations in your REDCap project. **
this file also sends an email to a specified email address each time an incoming SMS arrives. This is probably no longer neccessary, and could be done with an Alerts and Notifications trigger now.

## Alert to send outgoing messages
Finally, if you want to be able to send outgoing SMS messages from REDCap too, add the following to your alerts and notifications:
![image](https://user-images.githubusercontent.com/5281692/149120775-b7827c2f-4ff0-4f49-a60c-0066631edafc.png)

To send an SMS message to a participant, simply open the SMS instrument in their record, create a new outgoing instance, type your message and save it.

![image](https://user-images.githubusercontent.com/5281692/149121031-0b9e105e-1d3d-442d-aa4e-16214c343745.png)

You can then see your conversation history with the participant on the record home page:

![image](https://user-images.githubusercontent.com/5281692/149121191-4c58a8aa-28a1-4954-b3e2-4a8129fc94ef.png)

