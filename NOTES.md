

Well, that controller was awful and reminds me of what I'd have done about 15 years ago.
It's not maintainable, it's not really testable.

The plan was to spin out the database operations to repositories and business logic into a service layer. Methods doing one thing as per the name, not trying to cram everything into one.

I had two thoughts on the customer type problem. First, build an endpoint to allow people with sufficient permissions to add new ones. That felt like overkill so I went with an enum approach. Adding new ones then becomes the responsibility of a developer and nobody can go in to a database and change the discount amounts without anyone noticing. There's a commit history to anyone changing anything related to customer types.

I didn't get to including the SMS feature. That would need a new column on the customer model and the repository and getter/setter. Then a bit of research on SMS services and implement - memory suggests it's not dissimilar to sending emails. I think I used blade templates to do it in the past.

I've only just got this running locally and I've been learning Symfony as I go, especially what repositories can/can't do and parking my Laravel knowledge.
I wanted to add a custom request, but I don't think Symfony does this. Instead, I would add validation for the request data so that the default behaviour in the enum isn't needed and that emails, phone numbers etc are locked down to what we expect/allow. 
Clearly I'd want to fix up the tests as well. 