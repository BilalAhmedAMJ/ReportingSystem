#Roll-Up Questions
Roll-up questions are question that are aggregates from other questions, aggregate in a loose sense. Following "types" of aggregates are supported as of now:
* Direct copy: Aggregate answer is direct copy of source question answer
* Appended copy: Aggregate question is copy of source question answer, with branch name of source question appended to the aggregated answer
* Sum: Aggregate question's answer is sum of all source questions answers
* Average: Aggregate question's answer is average of all source questions answers

##Assumptions
* Only complex questions can be aggregate questions, i.e. a questoin that have child questions. Answer of parent/aggregate question have information on source used to calculate answer at aggregation. Once an aggregate question is manually edited, source calculation information is updated to indicate manual editting. 
* For now aggeregate is done only for the reports of "same reporting month" across child branches
* Aggregate operation is performed at the creation of a report and from that point onward, user is allowed ot update answer of the aggregate question manually
* User is allowed to update aggregate questoin, that will override manual entry for all child questions.
  